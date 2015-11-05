<?php

namespace spec\RulerZ;

use PhpSpec\Exception\Example\FailureException;
use RulerZ\Result\FilterResult;

trait FilterResultMatcherTrait
{
    public function getMatchers()
    {
        return [
            'returnResults' => function ($subject, $expectedResults) {
                if (!$subject instanceof FilterResult) {
                    throw new FailureException('The method did not return a FilterResult object');
                }

                $receivedResults = iterator_to_array($subject);

                if (count($receivedResults) !== count($expectedResults)) {
                    throw new FailureException(sprintf(
                        'Expected %d result, got %d',
                        count($expectedResults),
                        count($receivedResults)
                    ));
                }

                foreach ($receivedResults as $i => $result) {
                    $expectedResult = $expectedResults[$i];

                    if ($result !== $expectedResult) {
                        throw new FailureException(sprintf(
                            "Wrong result %d:\nExpected:\n%s\nActual:\n%s",
                            $i,
                            var_export($expectedResults, true),
                            var_export($results, true)
                        ));
                    }
                }

                return true;
            }
        ];
    }
}
