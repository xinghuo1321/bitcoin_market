<?php

namespace App\Services;

use App\Auth\BitcoindAuth as BTCAuth;
use App\Model\Wallet;
use Nette\Http\Request;

/**
 * 
 * @what Transaction id retriever from bitcoind
 * @author Tomáš Keske a.k.a клустерфцк
 * @copyright 2015-2016
 * 
 */

class TXidGetter {
    
    public function __construct(Request $r){
        if ($r->getMethod() !== "POST"){
            die();
            
        } else {
            $btcAuth = new BTCAuth();
            $btcClient = $btcAuth->btcd;
            $wallet = new Wallet($btcClient);
            $txID = $argv[1];

            $myfile = fopen("testfile.txt", "w");
            $context = $wallet->getTransaction($txID);
            fwrite($myfile, $context);
            fclose($myfile);
        }
    }
}










