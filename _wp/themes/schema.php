<?php
setJsonPankuzu($this_page_value);
?>
<?php if (is_singular('post')) :
	// https://developers.google.com/search/docs/data-types/video?hl=ja
	$scheme_array = array(
		"@context" => "http://schema.org",
		"@type" => "Article",
		"mainEntityOfPage" => array(
			"@type" => "WebPage",
			"@id" => $protocol . $_SERVER["HTTP_HOST"]
		),
		"name" => $title,
		"headline" => $title,
		"image" => $ogimage,
		"articleSection" => $description,
		"url" => $url,
		"datePublished" => get_the_time('Y/m/d'),
		"dateModified" => get_the_modified_date('Y/m/d'),
		"author" => array(
			"@type" => "Person",
			"url" => $protocol . $_SERVER["HTTP_HOST"],
			"name" => $client_name
		),
		"publisher" => array(
			"@type" => "Organization",
			"name" => $site_title,
			"logo" => array(
				"@type" => "ImageObject",
				"url" => $local_path . "/assets/image/common/logo.webp"
			)
		)
	);
	echo '<script type="application/ld+json">' . json_encode($scheme_array) . '</script>';

endif;
?>
<?php
if (is_singular('post')) :
	$faq_scheme = array(
		"@context" => "https://schema.org",
		"@type" => "FAQPage",
		"mainEntity" => array()
	);
	foreach (get_field('faq') as $dl) {
		$array = array(
			"@type" => "Question",
			"name" => $dl['dt'],
			"acceptedAnswer" => array(
				"@type" => "Answer",
				"text" => $dl['dd']
			)
		);
		$array['name'] 	= $title;
		array_push($faq_scheme['mainEntity'], $array);
	}
	echo '<script type="application/ld+json">' . json_encode($faq_scheme) . '</script>';
endif;
?>









<?php

// $logo_scheme = array(
// 	"@context" => "https://schema.org",
// 	"@type" => "Organization",
// 	"url" => "http://www.example.com",
// 	"logo" => "http://www.example.com/images/logo.webp"
// );



// $search_scheme = array(
// 	"@context" => "https://schema.org",
// 	"@type" => "WebSite",
// 	"url" => $protocol . $_SERVER["HTTP_HOST"],
// 	"potentialAction" => array(
// 		"@type" => "SearchAction",
// 		"target" => "https://query.example.com/search?q={search_term_string}",
// 		"query-input" => "required name=search_term_string"
// 	)
// );


// $product_sceme = array(
// 	"@context" => "https://schema.org/",
// 	"@type" => "Product",
// 	"name" => "Executive Anvil",
// 	"image" => array(
// 		"https://example.com/photos/1x1/photo.webp",
// 		"https://example.com/photos/4x3/photo.webp",
// 		"https://example.com/photos/16x9/photo.webp"
// 	),
// 	"description" => "Sleeker than ACME's Classic Anvil, the Executive Anvil is perfect for the business traveler looking for something to drop from a height.",
// 	"sku" => "0446310786",
// 	"mpn" => "925872",
// 	"brand" => array(
// 		"@type" => "Brand",
// 		"name" => "ACME"
// 	),
// 	"review" => array(
// 		"@type" => "Review",
// 		"reviewRating" => array(
// 			"@type" => "Rating",
// 			"ratingValue" => "4",
// 			"bestRating" => "5"
// 		),
// 		"author" => array(
// 			"@type" => "Person",
// 			"name" => "Fred Benson"
// 		)
// 	),
// 	"aggregateRating" => array(
// 		"@type" => "AggregateRating",
// 		"ratingValue" => "4.4",
// 		"reviewCount" => "89"
// 	),
// 	"offers" => array(
// 		"@type" => "Offer",
// 		"url" => "https://example.com/anvil",
// 		"priceCurrency" => "USD",
// 		"price" => "119.99",
// 		"priceValidUntil" => "2020-11-20",
// 		"itemCondition" => "https://schema.org/UsedCondition",
// 		"availability" => "https://schema.org/InStock"
// 	)
// );
// $shop_scheme = array(
// 	"@context" => "https://schema.org",
// 	"@type" => "Restaurant",
// 	"image" => array(
// 		"https://example.com/photos/1x1/photo.webp",
// 		"https://example.com/photos/4x3/photo.webp",
// 		"https://example.com/photos/16x9/photo.webp"
// 	),
// 	"@id" => "http://davessteakhouse.example.com",
// 	"name" => "Dave's Steak House",
// 	"address" => array(
// 		"@type" => "PostalAddress",
// 		"streetAddress" => "148 W 51st St",
// 		"addressLocality" => "New York",
// 		"addressRegion" => "NY",
// 		"postalCode" => "10019",
// 		"addressCountry" => "US"
// 	),
// 	"review" => array(
// 		"@type" => "Review",
// 		"reviewRating" => array(
// 			"@type" => "Rating",
// 			"ratingValue" => "4",
// 			"bestRating" => "5"
// 		),
// 		"author" => array(
// 			"@type" => "Person",
// 			"name" => "Lillian Ruiz"
// 		)
// 	),
// 	"geo" => array(
// 		"@type" => "GeoCoordinates",
// 		"latitude" => 40.761293,
// 		"longitude" => -73.982294
// 	),
// 	"url" => "http://www.example.com/restaurant-locations/manhattan",
// 	"telephone" => "+12122459600",
// 	"servesCuisine" => "American",
// 	"priceRange" => "$$$",
// 	"openingHoursSpecification" => array(
// 		array(
// 			"@type" => "OpeningHoursSpecification",
// 			"dayOfWeek" => array(
// 				"Monday",
// 				"Tuesday"
// 			),
// 			"opens" => "11:30",
// 			"closes" => "22:00"
// 		),
// 		array(
// 			"@type" => "OpeningHoursSpecification",
// 			"dayOfWeek" => array(
// 				"Wednesday",
// 				"Thursday",
// 				"Friday"
// 			),
// 			"opens" => "11:30",
// 			"closes" => "23:00"
// 		),
// 		array(
// 			"@type" => "OpeningHoursSpecification",
// 			"dayOfWeek" => "Saturday",
// 			"opens" => "16:00",
// 			"closes" => "23:00"
// 		),
// 		array(
// 			"@type" => "OpeningHoursSpecification",
// 			"dayOfWeek" => "Sunday",
// 			"opens" => "16:00",
// 			"closes" => "22:00"
// 		)
// 	),
// 	"menu" => "http://www.example.com/menu",
// 	"acceptsReservations" => "True"
// );
// $recruit_scheme = array(
// 	"@context" => "https://schema.org/",
// 	"@type" => "JobPosting",
// 	"title" => "Software Engineer",
// 	"description" => "<p>Google aspires to be an organization that reflects the globally diverse audience that our products and technology serve. We believe that in addition to hiring the best talent, a diversity of perspectives, ideas and cultures leads to the creation of better products and services.</p>",
// 	"identifier" => array(
// 		"@type" => "PropertyValue",
// 		"name" => "Google",
// 		"value" => "1234567"
// 	),
// 	"datePosted" => "2017-01-18",
// 	"validThrough" => "2017-03-18T00:00",
// 	"employmentType" => "CONTRACTOR",
// 	"hiringOrganization" => array(
// 		"@type" => "Organization",
// 		"name" => "Google",
// 		"sameAs" => "http://www.google.com",
// 		"logo" => "http://www.example.com/images/logo.webp"
// 	),
// 	"jobLocation" => array(
// 		"@type" => "Place",
// 		"address" => array(
// 			"@type" => "PostalAddress",
// 			"streetAddress" => "1600 Amphitheatre Pkwy",
// 			"addressLocality" => "Mountain View",
// 			"addressRegion" => "CA",
// 			"postalCode" => "94043",
// 			"addressCountry" => "US"
// 		)
// 	),
// 	"baseSalary" => array(
// 		"@type" => "MonetaryAmount",
// 		"currency" => "USD",
// 		"value" => array(
// 			"@type" => "QuantitativeValue",
// 			"value" => 40.00,
// 			"unitText" => "HOUR"
// 		)
// 	)
// );


?>
