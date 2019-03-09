<?php

namespace Modules\Ventas\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Log;

class Venta extends Model
{

    protected $table = 'ventas__ventas';
    public $translatedAttributes = [];
    protected $fillable = [
      'nro_factura', 'monto_total', 'datos_id', 'tipo_factura', 'monto_pagado',
      'total_iva', 'precio_total_letras', 'plazo_credito', 'razon_social',
      'ruc', 'direccion', 'telefono'
    ];

    public static $tipos_factura = [
      'contado' => 'Contado',
      'credito' => 'Crédito'
    ];

    public function detalles(){
      return $this->hasMany('Modules\Ventas\Entities\VentaDetalle');
    }
}
