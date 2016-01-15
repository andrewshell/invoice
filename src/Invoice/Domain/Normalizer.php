<?php declare(strict_types = 1);

namespace Invoice\Domain;

class Normalizer
{
    public function normalize(array $invoice, string $defaultNumber): array
    {
        $invoice['subtotal'] = 0;
        if (empty($invoice['number'])) {
            $invoice['number'] = $defaultNumber;
        }
        if (empty($invoice['date'])) {
            $invoice['date'] = 0;
        } elseif (is_string($invoice['date'])) {
            $invoice['date'] = strtotime($invoice['date']);
        }
        if (empty($invoice['paid'])) {
            $invoice['paid'] = 0;
        }
        if (empty($invoice['items'])) {
            $invoice['items'] = array();
        }
        if (is_array($invoice['items'])) {
            foreach (array_keys($invoice['items']) as $i) {
                $invoice['items'][$i] = array_merge(
                    [
                        'desc' => 'Unknown Item',
                        'unit_cost' => 0,
                        'quantity' => 1,
                    ],
                    $invoice['items'][$i]
                );
                $invoice['items'][$i]['price'] = (
                    $invoice['items'][$i]['unit_cost'] * $invoice['items'][$i]['quantity']
                );
                $invoice['subtotal'] += $invoice['items'][$i]['price'];
            }
        }
        $invoice['total'] = $invoice['subtotal'] - $invoice['paid'];

        return $invoice;
    }
}
