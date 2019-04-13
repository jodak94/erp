<?php

namespace Modules\Configuracion\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Configuracion\Entities\Configuracion;
use Modules\Configuracion\Http\Requests\CreateConfiguracionRequest;
use Modules\Configuracion\Http\Requests\UpdateConfiguracionRequest;
use Modules\Configuracion\Repositories\ConfiguracionRepository;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;
use Auth;
use Log;
class ConfiguracionController extends AdminBaseController
{
    /**
     * @var ConfiguracionRepository
     */
    private $configuracion;

    public function __construct(ConfiguracionRepository $configuracion)
    {
        parent::__construct();

        $this->configuracion = $configuracion;
    }

    public function configurar(){
      $query = Configuracion::orderBy('orden');
      $conf_factura = Configuracion::where('slug', 'factura')->orderBy('orden', 'asc')->get();
      $configuracions = Configuracion::
          where('slug', '!=', 'factura')
        ->where('slug', '!=', 'descuentos')
        ->where('admin', 0)
        ->orderBy('orden')
        ->get();
      $descuentos = Configuracion::where('slug', 'descuentos')->first();
      $descuentos->value = (array)json_decode($descuentos->value);
      return view('configuracion::admin.configuracions.configurar', compact('configuracions', 'conf_factura', 'descuentos'));
    }

    public function updateConfigurar(Request $request){
      $pad = 3;
      foreach ($request->fac_id as $key => $conf) {
        if($key == 2)
          $pad = 7;
        $conf = Configuracion::find($conf);
        $conf->value = str_pad($request->factura[$key], $pad, '0', STR_PAD_LEFT);
        $conf->save();
      }
      $configuracions =
      Configuracion::where('slug', '!=', 'factura')
        ->where('slug', '!=', 'descuentos')
        ->where('admin', 0)
        ->orderBy('orden')
        ->get();

      foreach ($configuracions as $conf) {
        $conf->value = $request[$conf->slug];
        $conf->save();
      }

      $descuentos = ['1' => '--'];
      if(isset($request->descuentos))
        foreach ($request->descuentos as $descuento) {
          $key = 1 - preg_replace( '/[^0-9]/', '', $descuento )/(float)100;
          $descuentos[(string)$key] = preg_replace( '/[^0-9]/', '', $descuento ) . '%';
        }
      $conf_descuento = Configuracion::where('slug', 'descuentos')->first();
      $conf_descuento->value = json_encode($descuentos);
      $conf_descuento->save();
      return redirect()->route('dashboard.index')->withSuccess('Configuración guardada');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    public function index()
    {
        $configuracions = Configuracion::orderBy('orden')->get();
        return view('configuracion::admin.configuracions.index', compact('configuracions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('configuracion::admin.configuracions.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CreateConfiguracionRequest $request
     * @return Response
     */
    public function store(CreateConfiguracionRequest $request)
    {
        $this->configuracion->create($request->all());

        return redirect()->route('admin.configuracion.configuracion.index')
            ->withSuccess(trans('core::core.messages.resource created', ['name' => trans('configuracion::configuracions.title.configuracions')]));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Configuracion $configuracion
     * @return Response
     */
    public function edit(Configuracion $configuracion)
    {
        return view('configuracion::admin.configuracions.edit', compact('configuracion'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Configuracion $configuracion
     * @param  UpdateConfiguracionRequest $request
     * @return Response
     */
    public function update(Configuracion $configuracion, UpdateConfiguracionRequest $request)
    {
        $this->configuracion->update($configuracion, $request->all());

        return redirect()->route('admin.configuracion.configuracion.index')
            ->withSuccess(trans('core::core.messages.resource updated', ['name' => trans('configuracion::configuracions.title.configuracions')]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Configuracion $configuracion
     * @return Response
     */
    public function destroy(Configuracion $configuracion)
    {
        $this->configuracion->destroy($configuracion);

        return redirect()->route('admin.configuracion.configuracion.index')
            ->withSuccess(trans('core::core.messages.resource deleted', ['name' => trans('configuracion::configuracions.title.configuracions')]));
    }
}