<?php
	// Finds the given label after start_point, then sets its value in value
	function get_label_value($country,$label,&$start_point,&$value) {
		$delim="\"".$label."\":\"";
		$str_start=strpos($country,$delim,$start_point);
		// If the label is not defined, we don't need to search further
		if ($str_start != false) {
			$str_start+=strlen($delim);
			$str_end=strpos($country,"\"",$str_start);
			$value=substr($country,$str_start,$str_end-$str_start);
			$start_point=$str_end;
		}
		else {
		    $start_point=false;
		}
	}

	// Get the input variables from javascript
	$rest_name = $_GET["name"];
	$rest_code = $_GET["code"];

	// Make the URL call to Rest Countries to get the data
	// We will format the URL so that it only returns data we need
	if (isset($rest_name) and $rest_name != null) {
		$rest_data = file_get_contents("https://restcountries.eu/rest/v2/name/".urlencode($rest_name)."?fields=name;alpha2Code;alpha3Code;region;subregion;population;flag;languages");
	}
	else if (isset($rest_code) and $rest_code != null) {
		$rest_data = file_get_contents("https://restcountries.eu/rest/v2/alpha/".urlencode($rest_code)."?fields=name;alpha2Code;alpha3Code;region;subregion;population;flag;languages");
	}

	if (!isset($rest_data)) {
		// If there is no data, return null
		echo json_encode(null);
	}
	else {
		// Now we need to get data for each individual country
		// First, we will split on '{"languages":', the first substring of each country
		$rest_countries=explode("{\"languages\":",$rest_data);

		// Now iterate over each country to build its object
		$i=0; // Index used to build array of country objects
		foreach($rest_countries as $rest_country) {
			if ($rest_country == "[") continue; // First substring, came before '{"languages":'

			// Next, split on "}]" to get only the languages section of the country
			$language_text=explode("}]",$rest_country)[0];
			unset($languages);
			$last_parsed=0;
			$j=0;
			do {
				// Iterate over each language and store it in an array
				get_label_value($language_text,"name",$last_parsed,$language);
				if ($last_parsed != false) {
					$languages[$j]=$language;
					$j++;
				}
			} while ($last_parsed != false);

			// Get the remaining properties
			$last_parsed=0;
			get_label_value($rest_country,"flag",$last_parsed,$flag);
			get_label_value($rest_country,"name",$last_parsed,$name);
			get_label_value($rest_country,"alpha2Code",$last_parsed,$alpha_two);
			get_label_value($rest_country,"alpha3Code",$last_parsed,$alpha_three);
			get_label_value($rest_country,"region",$last_parsed,$region);
			get_label_value($rest_country,"subregion",$last_parsed,$subregion);
			get_label_value($rest_country,"population",$last_parsed,$population);

			// Next we will increment the region and subregion counts
			if(!isset($region_count[$region])) { 
				$region_count[$region] = 1; 
			}
			else {
				$region_count[$region]++;
			}
			if(!isset($subregion_count[$subregion])) { 
				$subregion_count[$subregion] = 1; 
			}
			else {
				$subregion_count[$subregion]++;
			}

			// Lastly, we will build the actual object
			$countries[$i]=[
				"languages" => $languages,
				"flag" => $flag,
				"name" => $name,
				"alpha2" => $alpha_two,
				"alpha3" => $alpha_three,
				"region" => $region,
				"subregion" => $subregion,
				"population" => $population
			];
			$i++;
		}

		// Set the total countries count to be the total found, not the total displayed
		$total_countries=count($countries);

		// Sort the countries by name, and then by population
		usort($countries, function($country_one, $country_two)
		{
			$name_comp = strcmp(((object) $country_one)->name, ((object) $country_two)->name);
			if ($name_comp != 0) {
				$name_comp;
			}
			else {
				strcmp(((object) $country_one)->population, ((object) $country_two)->population);
			}
		});

		// We will only display the first 50 countries
		if ($total_countries>50) {
			$countries=array_slice($countries,0,50);
		}

		// Now store all the data in a single object for ease of transfer
		$final_data = [
			"countries" => $countries,
			"total" => $total_countries,
			"regions" => $region_count,
			"subregions" => $subregion_count
		];

		// Encode the data and send it back to javascript
		echo json_encode($final_data);
	}
?>