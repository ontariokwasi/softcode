<?php
require_once __DIR__ . '/connector.php';
require_once __DIR__ . '/../model/subs/product.php';
require_once __DIR__ . '/../constants.php';

class ProductsDao
{
    private mysqli $connector;
    public readonly string $table;
    public function __construct(?mysqli $connector)
    {
        global $con;
        $this->connector = isset($connector) ? $connector : $con;
        $this->table = "services";
    }

    public function getProductByOpId(string $opId, NetworkProvider $networkProvider): Product|bool
    {
        $network = $networkProvider->name;
        $stmt = "SELECT * FROM " . $this->table . " WHERE network='$network' AND op_id='$opId'";
        $result = mysqli_query($this->connector, $stmt);
        if ($row = mysqli_fetch_assoc($result)) {
            return new Product($row['id'], $row['name'], $row['op_id'], $row['network']);
        } else return false;
    }
}
