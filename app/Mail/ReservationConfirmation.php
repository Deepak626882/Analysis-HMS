<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReservationConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $company;
    public $data;
    public $rooms;
    public $advance;
    public $curdate;
    public $enviro;

    public function __construct($company, $data, $rooms, $advance, $curdate, $enviro)
    {
        $this->company = $company;
        $this->data = $data;
        $this->rooms = $rooms;
        $this->advance = $advance;
        $this->curdate = $curdate;
        $this->enviro = $enviro;
    }

    public function build()
    {
        return $this->view('property.resletter')
            ->subject('Reservation Confirmation')
            ->with([
                'company' => $this->company,
                'data' => $this->data,
                'rooms' => $this->rooms,
                'advance' => $this->advance,
                'curdate' => $this->curdate,
                'enviro' => $this->enviro,
            ]);
    }
}
