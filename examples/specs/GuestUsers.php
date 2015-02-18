<?php

namespace SampleSpecs;

use RulerZ\Spec\Specification;

class GuestUsers implements Specification
{
    public function getRule()
    {
        return 'group = :guest_group';
    }

    public function getParameters()
    {
        return [
            'guest_group' => 'guest',
        ];
    }
}
