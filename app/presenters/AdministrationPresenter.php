<?php

namespace App\Presenters;

use App\Model\Administration;
use Nette\Application\UI\Form;

/**
 * 
 * @what Market administration implementation
 * @author Tomáš Keske a.k.a клустерфцк
 * @copyright 2015-2016
 * 
 */

class AdministrationPresenter extends ProtectedPresenter
{
    protected $administration;
    
    public function injectAdministration(Administration $a){
        $this->administration = $a;
    }
    
    private function submitCreator($form, $hname, $fname ,$option, $value){
        
            $form->addSubmit($hname, $fname)->onClick[] = function() use($option, $value){
            $this->configuration->changeConfig($option, $value);
            $this->redirect("Administration:global");
        };
    }
    
    public function createComponentClickableSettings(){
                 
        $form = new Form();
 
        $maitenance = $this->configuration->isMarketInMaintenanceMode();
          
        if ($maitenance){
            
            $this->submitCreator($form, "dm", "Disable Maitenance Mode",
                    "maitenance", "off");   
        } else {
 
            $this->submitCreator($form, "em", "Enable Maitenance Mode",
                    "maitenance", "on");
        }
        
        $withdrawals = $this->configuration->areWithdrawalsEnabled();
        
        if ($withdrawals){
            
            $this->submitCreator($form, "dw", "Disable Withdrawals",
                    "withdrawals", "off");
        } else {
            
            $this->submitCreator($form, "ew", "Enable Withdrawals",
                    "withdrawals", "on");
        }
        
        $dosProtect = $this->configuration->isDosProtectionEnabled();
        
        if ($dosProtect){
            
            $this->submitCreator($form, "ddose", "Disable DOS Protect",
                    "dos_protection", "off");
        } else {
            
            $this->submitCreator($form, "ddosd", "Enable DOS Protect",
                    "dos_protection", "on");
        }
        
        $form->addSubmit("wn", "New Escrow Wallet Address")->onClick[] = function (){
            $address = $this->wallet->generateAddress("escrow");
            $this->configuration->changeConfig("escrow_address", $address);
            $this->redirect("Administration:global");
        };

        return $form;
    }
    
    public function createComponentFieldSettings(){
        
        $form = new Form();
        $optDB = $this->configuration->returnOptions();
        $this->hlp->sets("dbvals", $optDB);
        
        $form->addText("commision_percentage", "Market Commision")
             ->setValue($optDB["commision_percentage"])
             ->addRule($form::FLOAT, "V poli musí být vyplněna číselná hodnota!");
        
        $form->addSubmit("chbutton", "Change Configuration!");
        $form->addSubmit("ccbutton", "Cancel Changes")->onClick[] = function (){
            $this->redirect("Administration:global");
        };
        
        $form->onSuccess[] = array($this, "fieldSuccess");
        
        return $form;
    }
    
    public function fieldSuccess($form){
        
        if($form["chbutton"]->submittedBy){
        
            $values = $form->getValues(TRUE);   
            $optDB = $this->hlp->sess("dbvals");

            //update only changed values
            foreach($values as $key => $value){
               if ($value !== $optDB[$key]){
                   $this->configuration->changeConfig($key, $value);
               }
            }  
        }
    }
}