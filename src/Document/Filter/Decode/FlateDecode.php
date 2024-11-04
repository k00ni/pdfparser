<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Filter\Decode;

use PrinsFrank\PdfParser\Exception\GzUncompressException;
use PrinsFrank\PdfParser\Exception\ParseFailureException;

class FlateDecode {
    /** @throws GzUncompressException */
    public static function decode(string $value, LZWFlatePredictorValue $predictor, ?int $columns): string {
        $decodedValue = @gzuncompress($value);
        if ($decodedValue === false) {
            throw new GzUncompressException('Unable to gzuncompress value "' . substr(trim($value), 0, 30) . '..."');
        }

        $decodedValue = bin2hex($decodedValue);
        if ($predictor !== LZWFlatePredictorValue::None) {
            $hexTable = array_map(fn (string $row) => str_split($row, 2), str_split($decodedValue, ($columns + 1) * 2));
            $decodedValue = '';
            foreach ($hexTable as $rowIndex => $row) {
                $rowAlgorithm = PNGFilterAlgorithm::from((int) $row[0]);
                if ($rowAlgorithm !== PNGFilterAlgorithm::Up) {
                    throw new ParseFailureException(sprintf('PNG filters other than "Up" are currently not supported, "%s" given', $rowAlgorithm->name));
                }

                if ($rowIndex === 0) {
                    $decodedValue .= implode('', array_slice($row, 1));

                    continue; // We can't do an up transform with the top row
                }

                foreach ($row as $columnIndex => $columnValue) {
                    $row[$columnIndex] = str_pad(dechex((hexdec($columnValue) + hexdec($hexTable[$rowIndex - 1][$columnIndex])) % 255), 2, '0', STR_PAD_LEFT);
                }

                $decodedValue .= implode('', array_slice($row, 1));
            }
        }

        return $decodedValue;
    }
}
