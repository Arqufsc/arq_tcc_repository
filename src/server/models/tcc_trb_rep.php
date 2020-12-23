<?php

class tcc_trb_rep
{

    private $id;
    private $trb_id;
    private $link;

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setTrb_id($trbId)
    {
        $this->trb_id = $trbId;
    }

    public function setLink($link)
    {
        $this->link = $link;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTrb_id()
    {
        return $this->trb_id;
    }

    public function getLink()
    {
        return $this->link;
    }

}