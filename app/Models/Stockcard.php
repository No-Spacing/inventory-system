<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

use App\Models\Product;
use App\Models\Lot;
use App\Models\Serial;
use App\Models\Warehouse;

class Stockcard extends Model
{
    protected $fillable = [
        'product_id',
        'lot_id',
        'approver_id',
        'enduser_id',
        'serial_id',
        'transaction_type',
        'quantity',
        'transaction_date',
        'expiration_date',
        'document_no',
        'warehouse_id',
        'reference',
        'remarks',
    ];

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'enduser_id');
    }

    public function product(): HasOne
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

    public function lot(): HasOne
    {
        return $this->hasOne(Lot::class, 'id', 'lot_id');
    }

    public function serial(): HasOne
    {
        return $this->hasOne(Serial::class, 'id', 'serial_id');
    }

    public function warehouse(): HasOne
    {
        return $this->hasOne(Warehouse::class, 'id', 'warehouse_id');
    }

    public static function getProductStock($productId)
    {
        $query = self::where('product_id', $productId);

        return $query->selectRaw("
            SUM(CASE WHEN transaction_type = 'inbound' THEN quantity ELSE 0 END) -
            SUM(CASE WHEN transaction_type = 'outbound' THEN quantity ELSE 0 END) AS remaining
        ")->value('remaining') ?? 0;
    }
    
    public static function getWarehouseStock($warehouseId = null)
    {
        $query = self::where('warehouse_id', $warehouseId);

        return $query->selectRaw("
            SUM(CASE WHEN transaction_type = 'inbound' THEN quantity ELSE 0 END) -
            SUM(CASE WHEN transaction_type = 'outbound' THEN quantity ELSE 0 END) AS remaining
        ")->value('remaining') ?? 0;
    }

    public static function getProductWarehouseStock($productId, $warehouseId = null)
    {
        $query = self::where('product_id', $productId);

        if ($warehouseId) {
            $query->where('warehouse_id', $warehouseId);
        }

        return $query->selectRaw("
            SUM(CASE WHEN transaction_type = 'inbound' THEN quantity ELSE 0 END) -
            SUM(CASE WHEN transaction_type = 'outbound' THEN quantity ELSE 0 END) AS remaining
        ")->value('remaining') ?? 0;
    }

    public static function getLotsRemainingStock($productId, $lotId = null)
    {
        $query = self::where('product_id', $productId);

        $query->where('lot_id', $lotId);

        return $query->selectRaw("
            SUM(CASE WHEN transaction_type = 'inbound' THEN quantity ELSE 0 END) -
            SUM(CASE WHEN transaction_type = 'outbound' THEN quantity ELSE 0 END) AS remaining
        ")->value('remaining') ?? 0;
    }

    public static function getLotRemainingStock($productId, $lotId = null, $warehouseId = null)
    {
        $query = self::where('product_id', $productId);

        $query->where('lot_id', $lotId);
       
        $query->where('warehouse_id', $warehouseId);

        return $query->selectRaw("
            SUM(CASE WHEN transaction_type = 'inbound' THEN quantity ELSE 0 END) -
            SUM(CASE WHEN transaction_type = 'outbound' THEN quantity ELSE 0 END) AS remaining
        ")->value('remaining') ?? 0;
    }

    public static function getSerialRemainingStock($productId, $serialId = null, $warehouseId = null)
    {
        $query = self::where('product_id', $productId);

        $query->where('serial_id', $serialId);
 
        $query->where('warehouse_id', $warehouseId);

        return $query->selectRaw("
            SUM(CASE WHEN transaction_type = 'inbound' THEN quantity ELSE 0 END) -
            SUM(CASE WHEN transaction_type = 'outbound' THEN quantity ELSE 0 END) AS remaining
        ")->value('remaining') ?? 0;
    }

}
