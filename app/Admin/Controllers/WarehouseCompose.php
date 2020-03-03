<?php


namespace App\Admin\Controllers;


use App\Compose;
use App\Warehouse;

class WarehouseCompose
{

    protected $nowHave = [];

    /**
     * WarehouseCompose constructor.
     * @param array $status
     * @param null $company_id
     */
    public function __construct(array $status, $company_id = null)
    {
        if(!is_null($company_id)){
            $warehouse = Warehouse::whereIn('status', $status)->where('warehouse_company_id', $company_id)->get();
        }else{
            $warehouse = Warehouse::whereIn('status', $status)->get();
        }

        $this->nowHave = $warehouse->groupBy('product_id')->map(function ($item){
            return $item->sum('quantity');
        })->toArray();
    }

    public function getCompose()
    {
        $compose = Compose::with('composeProducts')->orderBy('order', 'DESC')->get();
        $compose = $compose->map(function ($item){
            $composeProducts = $item->composeProducts->pluck('quantity', 'product_id')->toArray();
            $count = $item->count;
            $have = 0;
            for ($i = 0; $i < $count ;$i++){
                $enough = $this->enough($composeProducts);
                $have = $enough ? ++$have : $have;
            }

            return [
                "id" => $item['id'],
//                "count" => $item['count'],
//                "name" => $item['name'],
//                "asin" => $item['asin'],
//                "image" => $item['image'],
                'have' => $have
            ];
        });

        return $compose->pluck('have', 'id')->toArray();
    }

    public function enough($composeProducts)
    {
        $enough = true;
        if(count(array_diff_key($composeProducts, $this->nowHave)) > 0){
            return false;
        }//无法构成组合

        foreach ($composeProducts as $key=>$product){
            if(isset($this->nowHave[$key])){
                if($this->nowHave[$key] < $product){
                    $enough = false; //单品数量不足构成组合
                    break;
                }
            }else{
                $enough = false; //缺少该单品
                break;
            }
        }

        if($enough){
            foreach ($composeProducts as $key=>$product){
                $this->nowHave[$key] = $this->nowHave[$key] - $product;
            }
        }
        return $enough;
    }

    /**
     * @return array
     */
    public function getNowHave(): array
    {
        return $this->nowHave;
    }
}
