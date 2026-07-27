<?php

namespace App\Models;

use CodeIgniter\Model;

class CountryModel extends Model
{
    protected $table = 'countries';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'name', 'code', 'phone_code', 'flag', 'region', 'status'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getActiveCountries()
    {
        return $this->where('status', 'active')->orderBy('name', 'ASC')->findAll();
    }

    public function getCountryByCode($code)
    {
        return $this->where('code', $code)->first();
    }

    public function getCountriesByRegion($region)
    {
        return $this->where('region', $region)->where('status', 'active')->orderBy('name', 'ASC')->findAll();
    }
}
