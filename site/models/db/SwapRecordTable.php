<?php

/*
 * An object to represent the the swap_records table.
 */

class SwapRecordTable extends Programster\MysqlObjects\AbstractTable
{
    public function getDb(): \mysqli
    {
        return SiteSpecific::getSwapsDatabase();
    }


    public function getFieldsThatAllowNull(): array
    {
        return array();
    }


    public function getFieldsThatHaveDefaults()
    {
        return array();
    }


    public function getObjectClassName()
    {
        return SwapRecord::class;
    }


    public function getTableName() { return "swap_records"; }


    public function validateInputs(array $data): array
    {
        return $data;
    }


    /**
     * Fetch the swap record for the specific user and barcodes.
     * @param string $deviceIdentifier
     * @param string $fromBarcode
     * @param string $toBarcode
     * @return \SwapRecord
     * @throws ExceptionSwapRecordNotFound
     */
    public function loadFromDeviceIdentifierAndBarcodes(
        string $deviceIdentifier,
        string $fromBarcode,
        string $toBarcode
    ) : SwapRecord
    {
        $records = $this->loadWhereAnd([
            'from_barcode' => $fromBarcode,
            'to_barcode' => $toBarcode,
            'device_identifier' => $deviceIdentifier,
        ]);

        if (count($records) !== 1)
        {
            throw new ExceptionSwapRecordNotFound();
        }

        /* @var $record SwapRecord */
        $record = $records[0];
        return $record;
    }


    /**
     * Create a swap tracking record, stating that a user swapped one product for another.
     * @param string $deviceIdentifier - the string for identifying a user (based on their device).
     * @param string $fromBarcode - the barcode of the product being swapped out.
     * @param string $toBarcode - the barcode of the healthier product being swapped to.
     * @return SwapRecord - the newly created SwapRecord
     */
    public function createFromDeviceIdentifierAndBarcodes(
        string $deviceIdentifier,
        string $fromBarcode,
        string $toBarcode
    ) : SwapRecord
    {
        return $this->create([
            'from_barcode' => $fromBarcode,
            'to_barcode' => $toBarcode,
            'device_identifier' => $deviceIdentifier,
            'when' => time(),
        ]);
    }


    /**
     * Fetch the records for swapping to the provided product barcode.
     * @param string $barcode - the barcode we wish to see records of being swapped to.
     * @return array - a collection of swap recordings.
     */
    public function loadFromBarcodeTo(string $barcode) : array
    {
        $wherePairs = array(
            'to_barcode' => $barcode
        );
        return $this->loadWhereAnd($wherePairs);
    }
}

