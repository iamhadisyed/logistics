<?php
namespace SmarttrackTransformer;

use Nocarrier\Hal;
use Smarttrack\ApiFunctions;

/**
 * Transform an Author (or collection of Authors) into Hal resource
 */
class WareHouseListTransformer
{

	public function transformCollection($warehouseList)
	{
//        $hal = new Hal('/api/get-tariffs');
		$hal = new Hal();
		$count = 0;
//		echo "<pre>"; print_r($userAccounts);echo "</pre>"; die();
		if(count($warehouseList) > 0){
			foreach ($warehouseList as $wareHouse) {
				$count++;
				$hal->addResource('warehouses', $this->transformAccess($wareHouse));
			}
			$hal->setData(['status' => 'success','status_code' => 200,'count' => $count,'message'=>"Records Found"]);
		}else{
			$hal->setData(['status' => 'error','status_code' => 400,'count' => $count,'message'=>"No Record Found!"]);
		}

		return $hal;
	}

	public function transformAccess($wareHouse)
	{
		$data =[
			'warehouseName'              =>  $wareHouse->getWarehouseName(),
			'warehouseCode'              =>  $wareHouse->getWarehouseCode(),
			'warehouseCountry'           =>  $wareHouse->getCountryName(),
			'warehouseCountryIso'        =>  $wareHouse->getCountryIso(),
			'warehouseEmail'             =>  $wareHouse->getEmail(),
			'warehousePhone'             =>  $wareHouse->getPhone(),

			'warehouseAddress1'         =>  $wareHouse->getAddressline1(),
			'warehouseAddress2'         =>  $wareHouse->getAddressline2(),
			'warehouseCity'             =>  $wareHouse->getCitytown(),
			'warehouseState'            =>  $wareHouse->getStateregion(),
			'warehouseZipCode'          =>  $wareHouse->getpostzipcode(),

		];
		$resource = new Hal(null, $data);
		return $resource;
	}
}
