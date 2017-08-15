<?php 
	class Musico extends Illuminate\Database\Eloquent\Model {
		protected $table = 'musico';
		public $timestamps = false;
		protected $primaryKey = 'id';

		public function pais(){
			return $this->belongsTo('Pais','c_pais_id');
		}

		public function horarios(){
			return $this->hasMany('Horario','musico_id');
		}

		public function excepciones(){
			return $this->hasMany('Excepcion','musico_id');
		}

		public function instrumentos(){
			return $this->hasMany('Instrumento','musico_id');
		}

		public function trajes(){
			return $this->hasMany('Traje','musico_id');
		}
	}

?>