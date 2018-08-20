<?php
	
	
	class WcOrderStatus
	{
		const PROCESSING = "processing";
		const ON_HOLD 	 = "on-hold";
		const CANCELLED  = "cancelled";
		const PENDING 	 = "pending";
		const FAILED 	 = "failed";
		const COMPLETED  = "completed";
		const REFUNDED 	 = "refunded";


		
		public static function getAllStatus()
		{
			$refl = new ReflectionClass('WcOrderStatus');
			return array_values($refl->getConstants());
		}
	}