<?php

namespace App\Http\Controllers;

use App\Service\BaseServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

class BaseController extends Controller implements BaseControllerInterface
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $baseService;
    public function __construct(BaseServiceInterface $baseService){
        $this->baseService = $baseService;
    }

    public function index(?Request $request, string $type = null): View|Collection|LengthAwarePaginator|null|callable|RedirectResponse
    {
        return View::class;
    }
}
