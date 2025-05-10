<?php

namespace Tinkoff\Invest\Services;

use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use InvalidArgumentException;
use Tinkoff\Invest\Models\Enums\CouponType;
use Tinkoff\Invest\Models\Enums\RealExchange;
use Tinkoff\Invest\Models\Enums\RiskLevel;
use Tinkoff\Invest\Models\Enums\SecurityTradingStatus;
use Tinkoff\Invest\Models\Instruments\Bonds\AccruedInterest;
use Tinkoff\Invest\Models\Instruments\Bonds\AssetBond;
use Tinkoff\Invest\Models\Instruments\Bonds\Bond;
use Tinkoff\Invest\Models\Instruments\Bonds\BondCollection;
use Tinkoff\Invest\Models\Instruments\Bonds\Coupon;
use Tinkoff\Invest\Models\DataTypes\MoneyValue;
use Tinkoff\Invest\Models\DataTypes\Quotation;
use Tinkoff\Invest\Transport\HttpClient;
use Tinkoff\Invest\Exceptions\ApiException;

/**
 * Сервис для работы с облигациями.
 *
 * @see https://tinkoff.github.io/investAPI/instruments/#bondservice
 */
final class BondsService
{
    /**
     * @param HttpClient $httpClient HTTP клиент
     */
    public function __construct(private HttpClient $httpClient)
    {
    }

    /**
     * Получает список всех облигаций.
     *
     * @return BondCollection Коллекция облигаций
     * @throws ApiException
     * @see https://tinkoff.github.io/investAPI/instruments/#bonds
     */
    public function getAllBonds(): BondCollection
    {
        $response = $this->httpClient->request(
            'POST',
            'tinkoff.public.invest.api.contract.v1.InstrumentsService/Bonds',
            ['instrumentStatus' => 'INSTRUMENT_STATUS_BASE']
        );

        return new BondCollection($this->transformBondsResponse($response));
    }

    /**
     * Получает данные по конкретной облигации по FIGI.
     *
     * @param string $figi FIGI облигации
     * @return Bond|null Данные облигации или null если не найдена
     * @throws Exception
     * @see https://tinkoff.github.io/investAPI/instruments/#bondby
     */
    public function getBondByFigi(string $figi): ?Bond
    {
        $response = $this->httpClient->request(
            'POST',
            'tinkoff.public.invest.api.contract.v1.InstrumentsService/BondBy',
            ['idType' => 'INSTRUMENT_ID_TYPE_FIGI', 'id' => $figi]
        );

        return isset($response['instrument']) ? $this->transformBond($response['instrument']) : null;
    }

    /**
     * Получает данные по конкретной облигации по ticker.
     *
     * @param string $ticker Ticker облигации
     * @param string $classCode Код класса инструмента (опционально)
     * @return Bond|null Данные облигации или null если не найдена
     * @throws Exception
     * @see https://tinkoff.github.io/investAPI/instruments/#bondby
     */
    public function getBondByTicker(string $ticker, string $classCode = ''): ?Bond
    {
        $response = $this->httpClient->request(
            'POST',
            'tinkoff.public.invest.api.contract.v1.InstrumentsService/BondBy',
            [
                'idType' => 'INSTRUMENT_ID_TYPE_TICKER',
                'id' => $ticker,
                'classCode' => $classCode
            ]
        );

        return isset($response['instrument']) ? $this->transformBond($response['instrument']) : null;
    }

    /**
     * Получает купоны облигации за период.
     *
     * @param string $figi FIGI облигации
     * @param DateTimeInterface $from Начало периода
     * @param DateTimeInterface $to Конец периода
     * @return Coupon[] Массив купонов
     * @throws ApiException
     * @see https://tinkoff.github.io/investAPI/instruments/#getbondcoupons
     */
    public function getBondCoupons(string $figi, DateTimeInterface $from, DateTimeInterface $to): array
    {
        $response = $this->httpClient->request(
            'POST',
            'tinkoff.public.invest.api.contract.v1.InstrumentsService/GetBondCoupons',
            [
                'figi' => $figi,
                'from' => $from->format(DateTimeInterface::ATOM),
                'to' => $to->format(DateTimeInterface::ATOM)
            ]
        );

        return $this->transformCouponsResponse($response['coupons'] ?? $response['events'] ?? []);
    }

    /**
     * Получает накопленный купонный доход (НКД) по облигации.
     *
     * @param string $figi FIGI облигации
     * @return AccruedInterest|null Данные НКД или null если не найдены
     * @throws ApiException
     * @see https://tinkoff.github.io/investAPI/instruments/#getaccruedinterests
     */
    public function getAccruedInterests(string $figi): ?AccruedInterest
    {
        $response = $this->httpClient->request(
            'POST',
            'tinkoff.public.invest.api.contract.v1.InstrumentsService/GetAccruedInterests',
            ['figi' => $figi]
        );

        if (empty($response['accruedInterests'])) {
            return null;
        }

        return $this->transformAccruedInterest($response['accruedInterests'][0]);
    }

    /**
     * Получает данные по активу облигации.
     *
     * @param string $assetUid UID актива облигации
     * @return AssetBond|null Данные по активу или null если не найдены
     * @throws ApiException
     * @see https://tinkoff.github.io/investAPI/instruments/#getassets
     */
    public function getAssetBond(string $assetUid): ?AssetBond
    {
        $response = $this->httpClient->request(
            'POST',
            'tinkoff.public.invest.api.contract.v1.InstrumentsService/GetAssets',
            []
        );

        // Ищем нужный актив среди всех
        foreach ($response['assets'] as $asset) {
            if ($asset['uid'] === $assetUid && $asset['type'] === 'ASSET_TYPE_BOND') {
                return $this->transformAssetBond($asset);
            }
        }

        return null;
    }

    /**
     * Преобразует ответ API в массив облигаций.
     *
     * @param array $response Ответ API
     * @return Bond[] Массив облигаций
     * @throws ApiException
     */
    private function transformBondsResponse(array $response): array
    {
        if (!isset($response['instruments']) || !is_array($response['instruments'])) {
            throw new ApiException('Invalid bonds response: missing instruments field');
        }

        $bonds = [];
        foreach ($response['instruments'] as $instrument) {
            try {
                $bonds[] = $this->transformBond($instrument);
            } catch (Exception) {
                continue;
            }
        }

        return $bonds;
    }

    /**
     * Преобразует данные облигации из API.
     *
     * @param array $data Данные облигации
     * @return Bond Объект облигации
     * @throws ApiException|Exception
     */
    private function transformBond(array $data): Bond
    {
        return new Bond(
            figi: $data['figi'],
            ticker: $data['ticker'],
            classCode: $data['classCode'] ?? '',
            isin: $data['isin'],
            lot: isset($data['lot']) ? (int)$data['lot'] : 1,
            currency: $data['currency'] ?? 'RUB',
            klong: isset($data['klong']) ? Quotation::fromApi($data['klong']) : new Quotation("0", 0),
            kshort: isset($data['kshort']) ? Quotation::fromApi($data['kshort']) : new Quotation("0", 0),
            dlong: isset($data['dlong']) ? Quotation::fromApi($data['dlong']) : new Quotation("0", 0),
            dshort: isset($data['dshort']) ? Quotation::fromApi($data['dshort']) : new Quotation("0", 0),
            dlongMin: isset($data['dlongMin']) ? Quotation::fromApi($data['dlongMin']) : new Quotation("0", 0),
            dshortMin: isset($data['dshortMin']) ? Quotation::fromApi($data['dshortMin']) : new Quotation("0", 0),
            shortEnabledFlag: (bool)($data['shortEnabledFlag'] ?? false),
            name: $data['name'],
            exchange: $data['exchange'],
            couponQuantityPerYear: isset($data['couponQuantityPerYear']) ? (int)$data['couponQuantityPerYear'] : 0,
            minPriceIncrement: isset($data['minPriceIncrement']) ? Quotation::fromApi($data['minPriceIncrement']) : new Quotation("0", 0),
            apiTradeAvailableFlag: (bool)($data['apiTradeAvailableFlag'] ?? false),
            realExchange: RealExchange::fromApi($data['realExchange']),
            maturityDate: isset($data['maturityDate']) ? new DateTimeImmutable($data['maturityDate']) : null,
            nominal: isset($data['nominal']) ? MoneyValue::fromApi($data['nominal']) : null,
            initialNominal: isset($data['initialNominal']) ? MoneyValue::fromApi($data['initialNominal']) : null,
            stateRegDate: isset($data['stateRegDate']) ? new DateTimeImmutable($data['stateRegDate']) : null,
            placementDate: isset($data['placementDate']) ? new DateTimeImmutable($data['placementDate']) : null,
            placementPrice: isset($data['placementPrice']) ? MoneyValue::fromApi($data['placementPrice']) : null,
            aciValue: isset($data['aciValue']) ? MoneyValue::fromApi($data['aciValue']) : null,
            countryOfRisk: $data['countryOfRisk'] ?? null,
            countryOfRiskName: $data['countryOfRiskName'] ?? null,
            sector: $data['sector'] ?? null,
            issueKind: $data['issueKind'] ?? null,
            issueSize: isset($data['issueSize']) ? (int)$data['issueSize'] : null,
            issueSizePlan: isset($data['issueSizePlan']) ? (int)$data['issueSizePlan'] : null,
            tradingStatus: isset($data['tradingStatus']) ? SecurityTradingStatus::fromApi($data['tradingStatus']) : null,
            otcFlag: (bool)($data['otcFlag'] ?? false),
            buyAvailableFlag: (bool)($data['buyAvailableFlag'] ?? false),
            sellAvailableFlag: (bool)($data['sellAvailableFlag'] ?? false),
            floatingCouponFlag: (bool)($data['floatingCouponFlag'] ?? false),
            perpetualFlag: (bool)($data['perpetualFlag'] ?? false),
            amortizationFlag: (bool)($data['amortizationFlag'] ?? false),
            uid: $data['uid'] ?? null,
            positionUid: $data['positionUid'] ?? null,
            forIisFlag: (bool)($data['forIisFlag'] ?? false),
            forQualInvestorFlag: (bool)($data['forQualInvestorFlag'] ?? false),
            weekendFlag: (bool)($data['weekendFlag'] ?? false),
            blockedTcaFlag: (bool)($data['blockedTcaFlag'] ?? false),
            subordinatedFlag: (bool)($data['subordinatedFlag'] ?? false),
            liquidityFlag: (bool)($data['liquidityFlag'] ?? false),
            first1minCandleDate: isset($data['first1minCandleDate']) ? new DateTimeImmutable($data['first1minCandleDate']) : null,
            first1dayCandleDate: isset($data['first1dayCandleDate']) ? new DateTimeImmutable($data['first1dayCandleDate']) : null,
            riskLevel: isset($data['riskLevel']) ? RiskLevel::fromApi($data['riskLevel']) : null
        );
    }

    /**
     * Преобразует данные купонов из API.
     *
     * @param array $couponsData Данные купонов
     * @return Coupon[] Массив купонов
     */
    private function transformCouponsResponse(array $couponsData): array
    {
        return array_filter(
            array_map(
                function (array $couponData): ?Coupon {
                    try {
                        return $this->transformCoupon($couponData);
                    } catch (Exception) {
                        return null;
                    }
                },
                $couponsData
            )
        );
    }

    /**
     * Преобразует данные одного купона из API.
     *
     * @param array $data Данные купона
     * @return Coupon Объект купона
     * @throws InvalidArgumentException|Exception
     */
    private function transformCoupon(array $data): Coupon
    {
        return new Coupon(
            figi: $data['figi'],
            couponDate: new DateTimeImmutable($data['couponDate']),
            couponNumber: (int)$data['couponNumber'],
            fixDate: isset($data['fixDate']) ? new DateTimeImmutable($data['fixDate']) : null,
            payOneBond: MoneyValue::fromApi($data['payOneBond']),
            couponType: CouponType::fromApi($data['couponType']),
            couponStartDate: new DateTimeImmutable($data['couponStartDate']),
            couponEndDate: new DateTimeImmutable($data['couponEndDate']),
            couponPeriod: (int)$data['couponPeriod']
        );
    }

    /**
     * Преобразует данные НКД из API.
     *
     * @param array $data Данные НКД
     * @return AccruedInterest Объект НКД
     * @throws InvalidArgumentException|Exception
     */
    private function transformAccruedInterest(array $data): AccruedInterest
    {
        return new AccruedInterest(
            date: new DateTimeImmutable($data['date']),
            value: Quotation::fromApi($data['value']),
            valuePercent: MoneyValue::fromApi($data['valuePercent']),
            nominal: MoneyValue::fromApi($data['nominal'])
        );
    }

    /**
     * Преобразует данные по активам облигации из API.
     *
     * @param array $asset Данные по активам
     * @return AssetBond Объект данных по активам
     * @throws InvalidArgumentException|Exception
     */
    private function transformAssetBond(array $asset): AssetBond
    {
        $bondData = $asset['instrument']['bond'] ?? [];

        return new AssetBond(
            uid: $asset['uid'],
            name: $asset['name'],
            isin: $asset['instrument']['isin'],
            ticker: $asset['instrument']['ticker'],
            currentNominal: MoneyValue::fromApi($bondData['currentNominal']),
            borrowName: $bondData['borrowName'] ?? null,
            issueSize: isset($bondData['issueSize']) ? MoneyValue::fromApi($bondData['issueSize']) : null,
            nominal: isset($bondData['nominal']) ? MoneyValue::fromApi($bondData['nominal']) : null,
            issueKind: $bondData['issueKind'] ?? null,
            document: $bondData['document'] ?? null,
            mortgageInfo: $bondData['mortgageInfo'] ?? null,
            collateralInfo: $bondData['collateralInfo'] ?? null,
            bondType: $bondData['bondType'] ?? null,
            basicSector: $bondData['basicSector'] ?? null
        );
    }
}
