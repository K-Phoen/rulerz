<?php

declare(strict_types=1);

namespace Entity;

class Player
{
    public $pseudo;

    public $fullname;

    public $gender;

    public $points;

    public $group;

    public $birthday;

    public function __construct(string $pseudo, string $fullname, string $gender, int $points, Group $group = null, \DateTime $birthday = null)
    {
        $this->pseudo = $pseudo;
        $this->fullname = $fullname;
        $this->gender = $gender;
        $this->points = $points;
        $this->group = $group;
        $this->birthday = $birthday;
    }
}
