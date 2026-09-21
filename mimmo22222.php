<?php
/**
 * Split Italian words into syllables.
 * @param string $word The word to split.
 * @return array An array of syllables.
 */
function splitItalianSyllables($word) {
    // Simple regex for Italian syllabification (simplified)
    return preg_split('/[aeiouy]/', $word);
}

/**
 * Check if a number is prime and calculate its square root.
 * @param int $num The number to check.
 * @return array [is_prime, sqrt]
 */
function checkPrimeAndSquareRoot($num) {
    // Check for prime
    $isPrime = true;
    if ($num <= 1) {
        $isPrime = false;
    } else {
        for ($i = 2; $i <= sqrt($num); $i++) {
            if ($num % $i == 0) {
                $isPrime = false;
                break;
            }
        }
    }

    // Calculate square root
    $sqrt = sqrt($num);

    return [
        'is_prime' => $isPrime,
        'square_root' => $sqrt
    ];
}

// Example usage (optional)
// echo "Syllables: " . implode(", ", splitItalianSyllables("casa")) . \n";
// $result = checkPrimeAndSquareRoot(13);
// echo "Is prime: " . ($result['is_prime'] ? 'Yes' : 'No') . ", Square root: " . $result['square_root'] . \n";
?>