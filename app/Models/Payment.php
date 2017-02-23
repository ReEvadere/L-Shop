<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Payment
 *
 * @author  D3lph1 <d3lph1.contact@gmail.com>
 *
 * @package App\Models
 */
class Payment extends Model
{
    protected $table = 'payments';

    protected $fillable = [
        'service',
        'products',
        'cost',
        'user_id',
        'username',
        'server_id',
        'ip',
        'complete',
        'created_at',
        'updated_at'
    ];
}
