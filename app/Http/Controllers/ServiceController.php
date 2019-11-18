<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\ServiceRepositoryInterface;

class ServiceController extends Controller
{
    private $serviceRepository;

    public function __construct(ServiceRepositoryInterface $serviceRepository)
    {
        $this->serviceRepository = $serviceRepository;
    }

    public function index()
    {
        $services = $this->serviceRepository->all();
        return response()->sendJsonSuccess($services);
    }
}
