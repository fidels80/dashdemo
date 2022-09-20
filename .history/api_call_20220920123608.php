$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://api.apilayer.com/exchangerates_data/latest?symbols={symbols}&base={base}",
  CURLOPT_HTTPHEADER => array(
    "Content-Type: text/plain",
    "apikey: TpwmpM4B98NQE4YXGgj20AFzRUqrTTWw"
  ),
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET"
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;