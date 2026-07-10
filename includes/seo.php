<?php
/**
 * SEO Helper Functions
 * JSON-LD Schema and Meta Tags for Accredited Inspection Agency
 */

/**
 * Generate Organization Schema (for Google Knowledge Panel)
 */
function get_organization_schema()
{
    return [
        "@context" => "https://schema.org",
        "@type" => "Organization",
        "name" => "Accredited Inspection Agency Private Limited",
        "alternateName" => "AIA",
        "url" => "https://accredited.co.in",
        "logo" => "https://accredited.co.in/assets/images/Agency_Logo.svg",
        "description" => "Professional Testing, Inspection and Certification services for steel, power, cement, and mining industries in India. ISO 9001:2015 certified.",
        "foundingDate" => "2025",
        "founder" => [
            "@type" => "Person",
            "name" => "Amit Joshi"
        ],
        "address" => [
            "@type" => "PostalAddress",
            "streetAddress" => "Near Khalsa Indan Gas Godam, Infront of Guru Dron School, Attarmuda",
            "addressLocality" => "Raigarh",
            "addressRegion" => "Chhattisgarh",
            "postalCode" => "496001",
            "addressCountry" => "IN"
        ],
        "contactPoint" => [
            "@type" => "ContactPoint",
            "telephone" => "+91-8001480096",
            "contactType" => "customer service",
            "email" => "info@accredited.co.in",
            "availableLanguage" => ["English", "Hindi"]
        ],
        "sameAs" => [
            "https://www.linkedin.com/company/accredited-inspection-agency"
        ],
        "areaServed" => [
            [
                "@type" => "Country",
                "name" => "India"
            ],
            [
                "@type" => "State",
                "name" => "Chhattisgarh"
            ]
        ],
        "knowsAbout" => [
            "Testing Services",
            "Inspection Services",
            "Sampling Services",
            "Coal Testing",
            "Steel Inspection",
            "Quality Certification",
            "Third Party Inspection"
        ]
    ];
}

/**
 * Generate LocalBusiness Schema (for local SEO)
 */
function get_local_business_schema()
{
    return [
        "@context" => "https://schema.org",
        "@type" => "ProfessionalService",
        "name" => "Accredited Inspection Agency",
        "image" => "https://accredited.co.in/assets/images/Agency_Logo.svg",
        "url" => "https://accredited.co.in",
        "telephone" => "+91-8001480096",
        "email" => "info@accredited.co.in",
        "address" => [
            "@type" => "PostalAddress",
            "streetAddress" => "Near Khalsa Indan Gas Godam, Infront of Guru Dron School, Attarmuda",
            "addressLocality" => "Raigarh",
            "addressRegion" => "Chhattisgarh",
            "postalCode" => "496001",
            "addressCountry" => "IN"
        ],
        "geo" => [
            "@type" => "GeoCoordinates",
            "latitude" => "21.8809",
            "longitude" => "83.4069"
        ],
        "openingHoursSpecification" => [
            "@type" => "OpeningHoursSpecification",
            "dayOfWeek" => ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"],
            "opens" => "09:00",
            "closes" => "18:00"
        ],
        "priceRange" => "$$",
        "aggregateRating" => [
            "@type" => "AggregateRating",
            "ratingValue" => "4.8",
            "reviewCount" => "50"
        ]
    ];
}

/**
 * Generate Service Schema
 */
function get_services_schema()
{
    return [
        "@context" => "https://schema.org",
        "@type" => "ItemList",
        "name" => "Testing, Inspection and Certification Services",
        "description" => "Professional TIC services offered by Accredited Inspection Agency",
        "itemListElement" => [
            [
                "@type" => "Service",
                "position" => 1,
                "name" => "Sampling Services",
                "description" => "Precise sampling as per IS, ISO, ASTM standards for mines, plants, and ports. Coal sampling, ore sampling, and mineral sampling services.",
                "provider" => ["@type" => "Organization", "name" => "Accredited Inspection Agency"],
                "serviceType" => "Industrial Sampling",
                "areaServed" => "India"
            ],
            [
                "@type" => "Service",
                "position" => 2,
                "name" => "Inspection Services",
                "description" => "Pre-shipment inspection, loading/unloading supervision, physical quality monitoring, and weighment assessment for commodities.",
                "provider" => ["@type" => "Organization", "name" => "Accredited Inspection Agency"],
                "serviceType" => "Third Party Inspection",
                "areaServed" => "India"
            ],
            [
                "@type" => "Service",
                "position" => 3,
                "name" => "Testing Services",
                "description" => "NABL accredited laboratory testing for coal, coke, minerals, ores, fertilizers, and environmental samples. ISO/IEC 17025:2017 compliant.",
                "provider" => ["@type" => "Organization", "name" => "Accredited Inspection Agency"],
                "serviceType" => "Laboratory Testing",
                "areaServed" => "India"
            ]
        ]
    ];
}

/**
 * Generate BreadcrumbList Schema
 */
function get_breadcrumb_schema($items)
{
    $itemListElement = [];
    foreach ($items as $position => $item) {
        $itemListElement[] = [
            "@type" => "ListItem",
            "position" => $position + 1,
            "name" => $item['name'],
            "item" => $item['url']
        ];
    }

    return [
        "@context" => "https://schema.org",
        "@type" => "BreadcrumbList",
        "itemListElement" => $itemListElement
    ];
}

/**
 * Generate FAQ Schema for featured snippets
 */
function get_faq_schema()
{
    return [
        "@context" => "https://schema.org",
        "@type" => "FAQPage",
        "mainEntity" => [
            [
                "@type" => "Question",
                "name" => "What services does Accredited Inspection Agency provide?",
                "acceptedAnswer" => [
                    "@type" => "Answer",
                    "text" => "Accredited Inspection Agency provides Testing, Inspection, and Certification (TIC) services including sampling, third-party inspection, and laboratory testing for coal, minerals, ores, and industrial commodities."
                ]
            ],
            [
                "@type" => "Question",
                "name" => "Is Accredited Inspection Agency ISO certified?",
                "acceptedAnswer" => [
                    "@type" => "Answer",
                    "text" => "Yes, Accredited Inspection Agency is ISO 9001:2015, ISO 14001:2015, and ISO 45001:2018 certified. We follow international standards for quality, environmental, and safety management."
                ]
            ],
            [
                "@type" => "Question",
                "name" => "Which industries does Accredited Inspection Agency serve?",
                "acceptedAnswer" => [
                    "@type" => "Answer",
                    "text" => "We serve steel, power, cement, mining, agriculture, and environmental industries across India, providing comprehensive inspection and testing services."
                ]
            ],
            [
                "@type" => "Question",
                "name" => "Where is Accredited Inspection Agency located?",
                "acceptedAnswer" => [
                    "@type" => "Answer",
                    "text" => "Accredited Inspection Agency is headquartered in Raigarh, Chhattisgarh, India. We provide services across India with a focus on the mineral-rich central Indian region."
                ]
            ],
            [
                "@type" => "Question",
                "name" => "How can I get a quote for inspection services?",
                "acceptedAnswer" => [
                    "@type" => "Answer",
                    "text" => "You can request a quote by visiting our contact page at accredited.co.in/contact.php, calling +91-8001480096, or emailing info@accredited.co.in."
                ]
            ]
        ]
    ];
}

/**
 * Generate WebSite Schema (for sitelinks searchbox)
 */
function get_website_schema()
{
    return [
        "@context" => "https://schema.org",
        "@type" => "WebSite",
        "name" => "Accredited Inspection Agency",
        "url" => "https://accredited.co.in",
        "potentialAction" => [
            "@type" => "SearchAction",
            "target" => "https://accredited.co.in/services.php?q={search_term_string}",
            "query-input" => "required name=search_term_string"
        ]
    ];
}

/**
 * Output all schemas as JSON-LD
 */
function output_schemas($page = 'home')
{
    $schemas = [];

    // Always include organization and website schemas
    $schemas[] = get_organization_schema();
    $schemas[] = get_website_schema();

    // Page-specific schemas
    switch ($page) {
        case 'home':
            $schemas[] = get_local_business_schema();
            $schemas[] = get_services_schema();
            $schemas[] = get_faq_schema();
            break;
        case 'about':
            $schemas[] = get_local_business_schema();
            break;
        case 'services':
            $schemas[] = get_services_schema();
            break;
        case 'contact':
            $schemas[] = get_local_business_schema();
            break;
    }

    foreach ($schemas as $schema) {
        echo '<script type="application/ld+json">' . "\n";
        echo json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        echo "\n</script>\n";
    }
}

/**
 * Get Open Graph meta tags
 */
function get_og_tags($title, $description, $url, $image = null)
{
    $image = $image ?? 'https://accredited.co.in/assets/images/Agency_Logo.svg';

    return [
        'og:type' => 'website',
        'og:site_name' => 'Accredited Inspection Agency',
        'og:title' => $title,
        'og:description' => $description,
        'og:url' => $url,
        'og:image' => $image,
        'og:locale' => 'en_IN',
        'twitter:card' => 'summary_large_image',
        'twitter:title' => $title,
        'twitter:description' => $description,
        'twitter:image' => $image
    ];
}

/**
 * Output Open Graph meta tags
 */
function output_og_tags($title, $description, $url, $image = null)
{
    $tags = get_og_tags($title, $description, $url, $image);

    foreach ($tags as $property => $content) {
        if (strpos($property, 'twitter:') === 0) {
            echo '<meta name="' . $property . '" content="' . htmlspecialchars($content) . '">' . "\n    ";
        } else {
            echo '<meta property="' . $property . '" content="' . htmlspecialchars($content) . '">' . "\n    ";
        }
    }
}
