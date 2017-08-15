<?php 
	class Horario extends Illuminate\Database\Eloquent\Model {
		protected $table = 'horario';
		protected $primaryKey = 'id';

		public function dia(){
			return $this->belongsTo('Dia','c_dia_id');
		}
	}

?>