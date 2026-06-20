<?php

// City suggestions powering the search autocomplete (served via /api/v1/cities,
// so the full list is never shipped to the browser). Add/extend freely — or point
// the CityController at a geocoding API / DB import for exhaustive worldwide coverage.
return [
    'list' => [
        // ===== India =====
        'Delhi', 'New Delhi', 'Mumbai', 'Bengaluru', 'Hyderabad', 'Chennai', 'Kolkata', 'Pune', 'Ahmedabad',
        'Jaipur', 'Goa', 'Patna', 'Lucknow', 'Kanpur', 'Nagpur', 'Indore', 'Bhopal', 'Chandigarh', 'Ludhiana',
        'Kochi', 'Thiruvananthapuram', 'Kozhikode', 'Coimbatore', 'Madurai', 'Tiruchirappalli', 'Visakhapatnam',
        'Vijayawada', 'Surat', 'Vadodara', 'Rajkot', 'Varanasi', 'Agra', 'Mathura', 'Amritsar', 'Jalandhar',
        'Udaipur', 'Jodhpur', 'Jaisalmer', 'Bikaner', 'Ajmer', 'Pushkar', 'Mount Abu', 'Rishikesh', 'Haridwar',
        'Dehradun', 'Nainital', 'Mussoorie', 'Shimla', 'Manali', 'Dharamshala', 'Dalhousie', 'Kasol', 'Spiti',
        'Srinagar', 'Gulmarg', 'Pahalgam', 'Leh', 'Kargil', 'Darjeeling', 'Gangtok', 'Guwahati', 'Shillong',
        'Tawang', 'Bhubaneswar', 'Puri', 'Cuttack', 'Ranchi', 'Jamshedpur', 'Raipur', 'Mysuru', 'Hampi',
        'Pondicherry', 'Ooty', 'Kodaikanal', 'Munnar', 'Alleppey', 'Kumarakom', 'Kovalam', 'Varkala', 'Wayanad',
        'Coorg', 'Chikmagalur', 'Mangaluru', 'Hubli', 'Belagavi', 'Aurangabad', 'Nashik', 'Shirdi', 'Lonavala',
        'Mahabaleshwar', 'Tirupati', 'Rameswaram', 'Kanyakumari', 'Port Blair', 'Havelock', 'Gaya', 'Bodh Gaya',
        'Prayagraj', 'Gorakhpur', 'Meerut', 'Noida', 'Gurugram', 'Faridabad', 'Jammu', 'Katra', 'Gwalior',
        'Khajuraho', 'Ujjain', 'Pachmarhi', 'Daman', 'Diu', 'Kufri', 'Auli', 'Ziro', 'Kohima', 'Imphal',
        'Agartala', 'Aizawl', 'Itanagar', 'Dispur', 'Siliguri', 'Asansol', 'Durgapur',

        // ===== Asia (rest) =====
        'Bangkok', 'Phuket', 'Pattaya', 'Krabi', 'Chiang Mai', 'Koh Samui', 'Singapore', 'Kuala Lumpur',
        'Langkawi', 'Penang', 'Bali', 'Denpasar', 'Jakarta', 'Lombok', 'Yogyakarta', 'Hanoi', 'Ho Chi Minh City',
        'Da Nang', 'Phnom Penh', 'Siem Reap', 'Vientiane', 'Yangon', 'Manila', 'Cebu', 'Boracay', 'Colombo',
        'Kandy', 'Galle', 'Malé (Maldives)', 'Kathmandu', 'Pokhara', 'Lumbini', 'Thimphu', 'Paro', 'Dhaka',
        'Chittagong', 'Tokyo', 'Osaka', 'Kyoto', 'Hokkaido', 'Seoul', 'Busan', 'Jeju', 'Beijing', 'Shanghai',
        'Guangzhou', 'Shenzhen', 'Chengdu', 'Xian', 'Hong Kong', 'Macau', 'Taipei', 'Ulaanbaatar', 'Almaty',
        'Astana', 'Tashkent', 'Samarkand', 'Bishkek', 'Baku', 'Tbilisi', 'Yerevan',

        // ===== Middle East =====
        'Dubai', 'Abu Dhabi', 'Sharjah', 'Doha', 'Muscat', 'Manama', 'Kuwait City', 'Riyadh', 'Jeddah',
        'Mecca', 'Medina', 'Amman', 'Petra', 'Beirut', 'Tel Aviv', 'Jerusalem', 'Istanbul', 'Antalya',
        'Cappadocia', 'Ankara', 'Tehran', 'Cairo', 'Sharm El Sheikh', 'Luxor',


        // ===== Europe =====
        'London', 'Manchester', 'Edinburgh', 'Dublin', 'Paris', 'Nice', 'Lyon', 'Amsterdam', 'Rotterdam',
        'Brussels', 'Berlin', 'Munich', 'Frankfurt', 'Hamburg', 'Cologne', 'Zurich', 'Geneva',
        'Interlaken', 'Lucerne', 'Vienna', 'Salzburg', 'Prague', 'Budapest', 'Warsaw', 'Krakow', 'Rome',
        'Milan', 'Venice', 'Florence', 'Naples', 'Madrid', 'Barcelona', 'Seville', 'Lisbon', 'Porto',
        'Athens', 'Santorini', 'Mykonos', 'Copenhagen', 'Stockholm', 'Oslo', 'Helsinki', 'Reykjavik',
        'Moscow', 'Saint Petersburg', 'Kyiv', 'Bucharest', 'Sofia', 'Belgrade', 'Zagreb', 'Dubrovnik',

        // ===== North America =====
        'New York', 'Los Angeles', 'San Francisco', 'Las Vegas', 'Chicago', 'Boston', 'Washington',
        'Miami', 'Orlando', 'Seattle', 'Houston', 'Dallas', 'Atlanta', 'Denver', 'San Diego', 'Hawaii',
        'Toronto', 'Vancouver', 'Montreal', 'Ottawa', 'Calgary', 'Mexico City', 'Cancun', 'Havana',

        // ===== South America =====
        'Rio de Janeiro', 'Sao Paulo', 'Buenos Aires', 'Lima', 'Cusco', 'Santiago', 'Bogota', 'Quito',
        'La Paz', 'Montevideo', 'Cartagena',

        // ===== Africa =====
        'Cape Town', 'Johannesburg', 'Nairobi', 'Mombasa', 'Zanzibar', 'Dar es Salaam', 'Marrakech',
        'Casablanca', 'Tunis', 'Lagos', 'Accra', 'Addis Ababa', 'Victoria (Seychelles)', 'Port Louis (Mauritius)',

        // ===== Oceania =====
        'Sydney', 'Melbourne', 'Brisbane', 'Perth', 'Gold Coast', 'Cairns', 'Auckland', 'Queenstown',
        'Wellington', 'Christchurch', 'Nadi (Fiji)', 'Bora Bora',
    ],
];
