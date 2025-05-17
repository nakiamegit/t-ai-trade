<?php

namespace Tinkoff\Invest\Services;

use DateTimeImmutable;
use DateTimeInterface;
use Tinkoff\Invest\Transport\HttpClientInterface;
use Tinkoff\Invest\Models\Instruments\Bonds\Bond;
use Tinkoff\Invest\Models\Instruments\Bonds\BondCollection;
use Tinkoff\Invest\Models\Instruments\Bonds\AssetBond;
use Tinkoff\Invest\Models\Instruments\Bonds\Coupon;
use Tinkoff\Invest\Models\Instruments\Bonds\BondEvent;
use Tinkoff\Invest\Models\Instruments\Bonds\AccruedInterest;
use Tinkoff\Invest\Models\Enums\BondEventType;
use Tinkoff\Invest\Models\Enums\CouponType;
use Tinkoff\Invest\Models\Enums\RealExchange;
use Tinkoff\Invest\Models\Enums\RiskLevel;
use Tinkoff\Invest\Models\Enums\SecurityTradingStatus;
use Tinkoff\Invest\Models\DataTypes\MoneyValue;
use Tinkoff\Invest\Models\DataTypes\Quotation;
use Tinkoff\Invest\Exceptions\Services\BondsServiceException;

/**
 * Сервис для работы с облигациями.
 *
 * @see https://tinkoff.github.io/investAPI/instruments/#bondservice
 */
final class BondsService
{
    private HttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Получает список всех облигаций.
     *
     * @return BondCollection Коллекция облигаций
     * @see https://tinkoff.github.io/investAPI/instruments/#bonds
     */
    public function getAllBonds(): BondCollection
    {
        try {
            $response = $this->httpClient->post(
                'tinkoff.public.invest.api.contract.v1.InstrumentsService/Bonds',
                ['instrumentStatus' => 'INSTRUMENT_STATUS_BASE']
            );

            if (!isset($response['instruments'])) {
                throw BondsServiceException::invalidBondResponse($response);
            }

            return $this->transformBondsResponse($response);
        } catch (\Throwable $e) {
            throw BondsServiceException::serviceUnavailable('GetAllBonds', $e);
        }
    }

    /**
     * Получает данные по конкретной облигации по FIGI.
     *
     * @param string $figi FIGI облигации
     * @return Bond|null Данные облигации или null если не найдена
     * @see https://tinkoff.github.io/investAPI/instruments/#bondby
     */
    public function getBondByFigi(string $figi): ?Bond
    {
        try {
            $response = $this->httpClient->request(
                'POST',
                'tinkoff.public.invest.api.contract.v1.InstrumentsService/BondBy',
                ['idType' => 'INSTRUMENT_ID_TYPE_FIGI', 'id' => $figi]
            );

            if (!isset($response['instrument'])) {
                return null;
            }

            return $this->transformBond($response['instrument']);
        } catch (\Throwable $e) {
            throw BondsServiceException::serviceUnavailable('GetBondByFigi', $e);
        }
    }

    /**
     * Получает данные по конкретной облигации по ticker.
     *
     * @param string $ticker Ticker облигации
     * @param string $classCode Код класса инструмента (опционально)
     * @return Bond|null Данные облигации или null если не найдена
     * @see https://tinkoff.github.io/investAPI/instruments/#bondby
     */
    public function getBondByTicker(string $ticker, string $classCode = ''): ?Bond
    {
        try {
            $response = $this->httpClient->request(
                'POST',
                'tinkoff.public.invest.api.contract.v1.InstrumentsService/BondBy',
                [
                    'idType' => 'INSTRUMENT_ID_TYPE_TICKER',
                    'id' => $ticker,
                    'classCode' => $classCode
                ]
            );

            if (!isset($response['instrument'])) {
                return null;
            }

            return $this->transformBond($response['instrument']);
        } catch (\Throwable $e) {
            throw BondsServiceException::serviceUnavailable('GetBondByTicker', $e);
        }
    }

    /**
     * Получает купоны облигации за период.
     *
     * @param string $figi FIGI облигации
     * @param DateTimeInterface $from Начало периода
     * @param DateTimeInterface $to Конец периода
     * @return Coupon[] Массив купонов
     * @throws BondsServiceException
     * @see https://tinkoff.github.io/investAPI/instruments/#getbondcoupons
     */
    public function getBondCoupons(string $figi, DateTimeInterface $from, DateTimeInterface $to): array
    {
        if ($from > $to) {
            throw BondsServiceException::invalidDateRange($from, $to);
        }

        try {
            $response = $this->httpClient->post(
                'tinkoff.public.invest.api.contract.v1.InstrumentsService/GetBondCoupons',
                [
                    'figi' => $figi,
                    'from' => $from->format(DateTimeInterface::ATOM),
                    'to' => $to->format(DateTimeInterface::ATOM)
                ]
            );

            if (!isset($response['coupons']) && !isset($response['events'])) {
                throw BondsServiceException::invalidCouponData($response);
            }

            $couponsData = $response['coupons'] ?? $response['events'];
            return $this->transformCouponsResponse($couponsData);
        } catch (\Throwable $e) {
            throw BondsServiceException::serviceUnavailable('GetBondCoupons', $e);
        }
    }

    /**
     * Получает накопленный купонный доход (НКД) по облигации.
     * Если даты не указаны, возвращает НКД на текущую дату.
     *
     * @param string $figi FIGI облигации
     * @param DateTimeInterface|null $from Начало периода (по умолчанию текущая дата)
     * @param DateTimeInterface|null $to Конец периода (по умолчанию текущая дата)
     * @return AccruedInterest|null Данные НКД или null если не найдены
     * @throws BondsServiceException
     * @see https://tinkoff.github.io/investAPI/instruments/#getaccruedinterests
     */
    public function getAccruedInterests(
        string             $figi,
        ?DateTimeInterface $from = null,
        ?DateTimeInterface $to = null
    ): ?AccruedInterest
    {
        $now = new DateTimeImmutable();
        $from = $from ?? $now;
        $to = $to ?? $now;

        if ($from > $to) {
            throw BondsServiceException::invalidDateRange($from, $to);
        }

        try {
            $response = $this->httpClient->request(
                'POST',
                'tinkoff.public.invest.api.contract.v1.InstrumentsService/GetAccruedInterests',
                [
                    'figi' => $figi,
                    'from' => $from->format(DateTimeInterface::ATOM),
                    'to' => $to->format(DateTimeInterface::ATOM)
                ]
            );

            if (empty($response['accruedInterests'])) {
                return null;
            }

            return $this->transformAccruedInterest($response['accruedInterests'][0]);
        } catch (\Throwable $e) {
            throw BondsServiceException::serviceUnavailable('GetAccruedInterests', $e);
        }
    }

    /**
     * Получает данные по активу облигации.
     *
     * @param string $assetUid UID актива облигации
     * @return AssetBond|null Данные по активу или null если не найдены
     * @throws BondsServiceException
     * @see https://tinkoff.github.io/investAPI/instruments/#getassets
     */
    public function getAssetBond(string $assetUid): ?AssetBond
    {
        try {
            $response = $this->httpClient->request(
                'POST',
                'tinkoff.public.invest.api.contract.v1.InstrumentsService/GetAssets',
                []
            );

            if (!isset($response['assets'])) {
                throw BondsServiceException::invalidAssetData($response);
            }

            foreach ($response['assets'] as $asset) {
                if ($asset['uid'] === $assetUid && $asset['type'] === 'ASSET_TYPE_BOND') {
                    return $this->transformAssetBond($asset);
                }
            }

            return null;
        } catch (\Throwable $e) {
            throw BondsServiceException::serviceUnavailable('GetAssets', $e);
        }
    }

    /**
     * Получает события по облигации за период.
     *
     * @param string $instrumentId Идентификатор инструмента (figi или instrument_uid)
     * @param DateTimeInterface $from Начало периода
     * @param DateTimeInterface $to Конец периода
     * @param BondEventType|null $type Тип события (опционально)
     * @return BondEvent[] Массив событий
     * @throws BondsServiceException
     * @see https://developer.tbank.ru/invest/services/instruments/methods#getbondeventsrequesteventtype
     */
    public function getBondEvents(
        string $instrumentId,
        DateTimeInterface $from,
        DateTimeInterface $to,
        ?BondEventType $type = null
    ): array {
        if ($from > $to) {
            throw BondsServiceException::invalidDateRange($from, $to);
        }

        try {
            $params = [
                'instrument_id' => $instrumentId,
                'from' => $from->format(DateTimeInterface::ATOM),
                'to' => $to->format(DateTimeInterface::ATOM)
            ];

            if ($type !== null) {
                $params['type'] = $type->value;
            }

            $response = $this->httpClient->request(
                'POST',
                'tinkoff.public.invest.api.contract.v1.InstrumentsService/GetBondEvents',
                $params
            );

            if (!isset($response['events'])) {
                throw BondsServiceException::invalidBondEvent($response);
            }

            return $this->transformBondEventsResponse($response['events']);
        } catch (\Throwable $e) {
            throw BondsServiceException::serviceUnavailable('GetBondEvents', $e);
        }
    }

    private function transformBondsResponse(array $response): BondCollection
    {
        if (!isset($response['instruments']) || !is_array($response['instruments'])) {
            throw BondsServiceException::invalidBondResponse($response);
        }

        $bonds = [];
        foreach ($response['instruments'] as $instrument) {
            try {
                $bonds[] = $this->transformBond($instrument);
            } catch (BondsServiceException) {
                continue;
            } catch (\Throwable $e) {
                throw BondsServiceException::invalidBondResponse($response);
            }
        }

        return new BondCollection($bonds);
    }

    /**
     * Преобразует данные облигации из API.
     *
     * @param array $data Данные облигации
     * @return Bond Объект облигации
     * @throws BondsServiceException|\Exception
     */
    private function transformBond(array $data): Bond
    {
        $requiredFields = ['figi', 'ticker', 'name', 'lot', 'currency'];
        $missingFields = array_diff($requiredFields, array_keys($data));

        if (!empty($missingFields)) {
            throw BondsServiceException::invalidBondResponse([
                'message' => 'Missing required bond fields',
                'missingFields' => $missingFields
            ]);
        }

        try {
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
        } catch (\InvalidArgumentException $e) {
            throw BondsServiceException::invalidBondResponse([
                'message' => 'Invalid bond field values',
                'error' => $e->getMessage()
            ]);
        }
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
                    } catch (\Exception $e) {
                        throw BondsServiceException::invalidCouponData($couponData);
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
     * @throws BondsServiceException
     */
    private function transformCoupon(array $data): Coupon
    {
        $requiredFields = [
            'figi', 'couponDate', 'couponNumber', 'payOneBond',
            'couponType', 'couponStartDate', 'couponEndDate', 'couponPeriod'
        ];

        $missingFields = array_diff($requiredFields, array_keys($data));
        if (!empty($missingFields)) {
            throw BondsServiceException::invalidCouponData([
                'message' => 'Missing required coupon fields',
                'missingFields' => $missingFields
            ]);
        }

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
     * @throws BondsServiceException|\Exception
     */
    private function transformAccruedInterest(array $data): AccruedInterest
    {
        $requiredFields = ['date', 'value', 'valuePercent', 'nominal'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                throw BondsServiceException::invalidAccruedInterest($data);
            }
        }

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
     * @throws BondsServiceException
     */
    private function transformAssetBond(array $asset): AssetBond
    {
        if (!isset($asset['uid'], $asset['name'], $asset['instrument']['isin'], $asset['instrument']['ticker'])) {
            throw BondsServiceException::invalidAssetData($asset);
        }

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

    /**
     * Преобразует данные событий облигации из API.
     *
     * @param array $eventsData Данные событий
     * @return BondEvent[] Массив событий
     */
    private function transformBondEventsResponse(array $eventsData): array
    {
        return array_filter(
            array_map(
                function (array $eventData): ?BondEvent {
                    try {
                        return $this->transformBondEvent($eventData);
                    } catch (\Exception $e) {
                        throw BondsServiceException::invalidBondEvent($eventData);
                    }
                },
                $eventsData
            )
        );
    }

    /**
     * Преобразует данные одного события облигации из API.
     *
     * @param array $data Данные события
     * @return BondEvent Объект события
     * @throws BondsServiceException|\Exception
     */
    private function transformBondEvent(array $data): BondEvent
    {
        $requiredFields = ['instrumentId', 'eventNumber', 'eventDate', 'eventType', 'eventTotalVol'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                throw BondsServiceException::invalidBondEvent($data);
            }
        }

        return new BondEvent(
            instrumentId: $data['instrumentId'] ?? '',
            eventNumber: (int)($data['eventNumber'] ?? 0),
            eventDate: new DateTimeImmutable($data['eventDate']),
            eventType: BondEventType::fromApi($data['eventType'] ?? 'EVENT_TYPE_UNSPECIFIED'),
            eventTotalVol: Quotation::fromApi($data['eventTotalVol'] ?? ['units' => '0', 'nano' => 0]),
            fixDate: isset($data['fixDate']) ? new DateTimeImmutable($data['fixDate']) : null,
            rateDate: isset($data['rateDate']) ? new DateTimeImmutable($data['rateDate']) : null,
            defaultDate: isset($data['defaultDate']) ? new DateTimeImmutable($data['defaultDate']) : null,
            realPayDate: isset($data['realPayDate']) ? new DateTimeImmutable($data['realPayDate']) : null,
            payDate: isset($data['payDate']) ? new DateTimeImmutable($data['payDate']) : null,
            payOneBond: isset($data['payOneBond']) ? MoneyValue::fromApi($data['payOneBond']) : null,
            moneyFlowVal: isset($data['moneyFlowVal']) ? MoneyValue::fromApi($data['moneyFlowVal']) : null,
            execution: $data['execution'] ?? null,
            operationType: $data['operationType'] ?? null,
            value: isset($data['value']) ? Quotation::fromApi($data['value']) : null,
            note: $data['note'] ?? null,
            convertToFinToolId: $data['convertToFinToolId'] ?? null,
            couponStartDate: isset($data['couponStartDate']) ? new DateTimeImmutable($data['couponStartDate']) : null,
            couponEndDate: isset($data['couponEndDate']) ? new DateTimeImmutable($data['couponEndDate']) : null,
            couponPeriod: isset($data['couponPeriod']) ? (int)$data['couponPeriod'] : null,
            couponInterestRate: isset($data['couponInterestRate']) ? Quotation::fromApi($data['couponInterestRate']) : null
        );
    }
}

