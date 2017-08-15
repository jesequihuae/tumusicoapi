<?php 
	class Estado extends Illuminate\Database\Eloquent\Model {
		protected $table = 'c_estado';
		protected $primaryKey = 'id';

		public function ciudades(){
			return $this->hasMany('Ciudad','c_estado_id');
		}

	}

?>