<?php
class Quote
{
	const SURCHARGE_WEIGHT = 70;
	const SURCHARGE_AMOUNT = 25;
	//
	private static $active_services_only = true;
	private static $message = "";
	private $service;
	private $parcel_group;
	private $tariff_array = array();
    private $parcel_tariff;

	/**
	 * Create an instance of price calculation with required
	 * settings
	 *
	 */
	private function __construct($parcel_group, $tariff_array, $service)
	{
		$this->parcel_group = $parcel_group;
		$this->tariff_array = $tariff_array;
		$this->service = $service;
	}

	/**
	 * Clas level message
	 *
	 * @return string
	 */
	public static function getMessage()
	{
		return self::$message;
	}

	public function getTariffId()
	{
		if(isset($this->tariff_array[0]))
        {
        $val=is_null($this->tariff_array[0]);
        if(!$val||$val>0)
        {
        $test="sdgsdfg";
        $tariff= $this->tariff_array[0];
        return $this->tariff_array[0]->getId();
        }

        }

    return "";
    }

	private function getService()
	{
		return $this->service;
	}

	public function getServiceId()
	{
		return $this->service->getId();
	}

	public function getLogo()
	{
		return $this->service->getLogo();
	}

	public function getServiceName()
	{
		return $this->service->getName();
	}

	public function getPrice()
	{
		$price = 0;
        $serviceName=$this->getServiceId();

        if(isset($this->tariff_array[0]))
        {
        if(!is_null($this->tariff_array[0]))
                                {
        if(Sessionmanager::getCustomerId()==72 || Sessionmanager::getCustomerId()==76 || Sessionmanager::getCustomerId()==81 || Sessionmanager::getCustomerId()==83 || Sessionmanager::getCustomerId()==89 )
        {
            switch($serviceName)
            {
                case "2":
                {
                     $firstQuote=true;

            foreach ($this->tariff_array as $tariff)
                                {
            //if($tariff->getPrice()<=$cheapestPrice)
           // {
            //$price=$tariff->getPrice();
            if($firstQuote==true)
            {
                $tariff->setTariff("20");
                $firstQuote=false;
            }

            else
            {
                $tariff->setTariff("17");
            }
            $cheapestPrice=$price;
            $price += $tariff->getPrice();

        //}

                                }

                return $price;
                }

                case "12":
                {
                     $firstQuote=true;

            foreach ($this->tariff_array as $tariff)
                                {
            //if($tariff->getPrice()<=$cheapestPrice)
           // {
            //$price=$tariff->getPrice();
            if($firstQuote==true)
            {
                $tariff->setTariff("30");
                $firstQuote=false;
            }

            else
            {
                $tariff->setTariff("10");
            }
            $cheapestPrice=$price;
            $price += $tariff->getPrice();

        //}

                                }

                return $price;
                }

                case "11":
                {
                     $firstQuote=true;

            foreach ($this->tariff_array as $tariff)
                                {
            //if($tariff->getPrice()<=$cheapestPrice)
           // {
            //$price=$tariff->getPrice();
            if($firstQuote==true)
            {
                $tariff->setTariff("35");
                $firstQuote=false;
            }

            else
            {
                $tariff->setTariff("15");
            }
            $cheapestPrice=$price;
            $price += $tariff->getPrice();

        //}

                                }

                return $price;
                }

                case "14":
                {
                     $firstQuote=true;

            foreach ($this->tariff_array as $tariff)
                                {
            //if($tariff->getPrice()<=$cheapestPrice)
           // {
            //$price=$tariff->getPrice();
            if($firstQuote==true)
            {
                $tariff->setTariff("50");
                $firstQuote=false;
            }

            else
            {
                $tariff->setTariff("10");
            }
            $cheapestPrice=$price;
            $price += $tariff->getPrice();

        //}

                                }

                return $price;
                }

                case "13":
                {
                     $firstQuote=true;

            foreach ($this->tariff_array as $tariff)
                                {
            //if($tariff->getPrice()<=$cheapestPrice)
           // {
            //$price=$tariff->getPrice();
            if($firstQuote==true)
            {
                $tariff->setTariff("20");
                $firstQuote=false;
            }

            else
            {
                $tariff->setTariff("20");
            }
            $cheapestPrice=$price;
            $price += $tariff->getPrice();

        //}

                                }

                return $price;
                }

                case "18":
                {
                     $firstQuote=true;

            foreach ($this->tariff_array as $tariff)
                                {
            //if($tariff->getPrice()<=$cheapestPrice)
           // {
            //$price=$tariff->getPrice();
            if($firstQuote==true)
            {
                $tariff->setTariff("40");
                $firstQuote=false;
            }

            else
            {
                $tariff->setTariff("30");
            }
            $cheapestPrice=$price;
            $price += $tariff->getPrice();

        //}

                                }

                return $price;
                }

                case "21":
                {
                     $firstQuote=true;

            foreach ($this->tariff_array as $tariff)
                                {
            //if($tariff->getPrice()<=$cheapestPrice)
           // {
            //$price=$tariff->getPrice();
            if($firstQuote==true)
            {
                $tariff->setTariff("55");
                $firstQuote=false;
            }

            else
            {
                $tariff->setTariff("45");
            }
            $cheapestPrice=$price;
            $price += $tariff->getPrice();

        //}

                                }

                return $price;
                }

            }
        }
                                }



        }

        if(isset($this->tariff_array[0]))
        {
		if(!is_null($this->tariff_array[0]))
                                {
       //$price=$this->tariff_array[0]->getPrice();
       $cheapestPrice=$price; // gets the first value in the array
        foreach ($this->tariff_array as $tariff)
		{
			//if($tariff->getPrice()<=$cheapestPrice)
           // {
            //$price=$tariff->getPrice();
            $serviceId=$tariff->getService();
            $cheapestPrice=$price;
            $serviceId=$this->getServiceId();
       // if(!Sessionmanager::isLoggedIn())
       // {
            if($serviceId=="2"||$serviceId=="12"||$serviceId=="11"||$serviceId=="14"||$serviceId=="13"||$serviceId=="18"||$serviceId=="21")
            {
                $price += $tariff->getPrice()+3;
            }

             else
             {
            $price += $tariff->getPrice();
             }

      //  }

       // else
       // {
               //$price += $tariff->getPrice();
      //  }


		//}
                                }
                                }
		}
        return $price;


    }
    
}
?>