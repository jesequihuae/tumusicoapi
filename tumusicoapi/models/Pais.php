<?php 
	class Pais extends Illuminate\Database\Eloquent\Model {
		protected $table = 'c_pais';
		protected $primaryKey = 'id';

		public function estados(){
			return $this->hasMany('Estado','c_pais_id');
		}

		public function musicos(){
			return $this->hasMany('Musico','id');
		}

	}

?>