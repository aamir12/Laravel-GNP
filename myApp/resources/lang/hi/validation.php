<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'The :attribute must be accepted.',
    'active_url' => 'The :attribute is not a valid URL.',
    'after' => 'The :attribute must be a date after :date.',
    'after_or_equal' => 'The :attribute must be a date after or equal to :date.',
    'alpha' => ':attribute में केवल अक्षर हो सकते हैं।',
    'alpha_dash' => 'The :attribute may only contain letters, numbers, dashes and underscores.',
    'alpha_num' => 'The :attribute may only contain letters and numbers.',
    'array' => 'The :attribute must be an array.',
    'before' => 'The :attribute must be a date before :date.',
    'before_or_equal' => 'The :attribute must be a date before or equal to :date.',
    'between' => [
        'numeric' => 'The :attribute must be between :min and :max.',
        'file' => 'The :attribute must be between :min and :max kilobytes.',
        'string' => 'The :attribute must be between :min and :max characters.',
        'array' => 'The :attribute must have between :min and :max items.',
    ],
    'boolean' => ':attribute फ़ील्ड बूलियन होना चाहिए।',
    'confirmed' => 'The :attribute confirmation does not match.',
    'date' => 'The :attribute is not a valid date.',
    'date_equals' => 'The :attribute must be a date equal to :date.',
    'date_format' => 'The :attribute does not match the format :format.',
    'different' => ':attribute और :other अलग-अलग होनी चाहिए।',
    'digits' => 'The :attribute must be :digits digits.',
    'digits_between' => 'The :attribute must be between :min and :max digits.',
    'dimensions' => 'The :attribute has invalid image dimensions.',
    'distinct' => 'The :attribute field has a duplicate value.',
    'email' => ':attribute एक मान्य ईमेल पता होना चाहिए।',
    'ends_with' => 'The :attribute must end with one of the following: :values',
    'exists' => 'दिया गया :attribute अमान्य है।',
    'file' => 'The :attribute must be a file.',
    'filled' => 'The :attribute field must have a value.',
    'gt' => [
        'numeric' => 'The :attribute must be greater than :value.',
        'file' => 'The :attribute must be greater than :value kilobytes.',
        'string' => 'The :attribute must be greater than :value characters.',
        'array' => 'The :attribute must have more than :value items.',
    ],
    'gte' => [
        'numeric' => 'The :attribute must be greater than or equal :value.',
        'file' => 'The :attribute must be greater than or equal :value kilobytes.',
        'string' => 'The :attribute must be greater than or equal :value characters.',
        'array' => 'The :attribute must have :value items or more.',
    ],
    'image' => 'The :attribute must be an image.',
    'in' => 'The selected :attribute is invalid.',
    'in_array' => 'The :attribute field does not exist in :other.',
    'integer' => ':attribute एक पूर्णांक संख्या होनी चाहिए।',
    'ip' => 'The :attribute must be a valid IP address.',
    'ipv4' => 'The :attribute must be a valid IPv4 address.',
    'ipv6' => 'The :attribute must be a valid IPv6 address.',
    'json' => ':attribute एक वैध जयसन स्ट्रिंग होनी चाहिए।',
    'lt' => [
        'numeric' => 'The :attribute must be less than :value.',
        'file' => 'The :attribute must be less than :value kilobytes.',
        'string' => 'The :attribute must be less than :value characters.',
        'array' => 'The :attribute must have less than :value items.',
    ],
    'lte' => [
        'numeric' => 'The :attribute must be less than or equal :value.',
        'file' => 'The :attribute must be less than or equal :value kilobytes.',
        'string' => 'The :attribute must be less than or equal :value characters.',
        'array' => 'The :attribute must not have more than :value items.',
    ],
    'max' => [
        'numeric' => 'The :attribute may not be greater than :max.',
        'file' => 'The :attribute may not be greater than :max kilobytes.',
        'string' => 'The :attribute may not be greater than :max characters.',
        'array' => 'The :attribute may not have more than :max items.',
    ],
    'mimes' => 'The :attribute must be a file of type: :values.',
    'mimetypes' => 'The :attribute must be a file of type: :values.',
    'min' => [
        'numeric' => 'The :attribute must be at least :min.',
        'file' => 'The :attribute must be at least :min kilobytes.',
        'string' => 'The :attribute must be at least :min characters.',
        'array' => 'The :attribute must have at least :min items.',
    ],
    'not_in' => 'The selected :attribute is invalid.',
    'not_regex' => 'The :attribute format is invalid.',
    'numeric' => ':attribute एक संख्या होनी चाहिए।',
    'present' => 'The :attribute field must be present.',
    'regex' => ':attribute का स्वरूप अमान्य है।',
    'required' => ':attribute आवश्यक है।',
    'required_if' => 'The :attribute field is required when :other is :value.',
    'required_unless' => 'The :attribute field is required unless :other is in :values.',
    'required_with' => 'The :attribute field is required when :values is present.',
    'required_with_all' => 'The :attribute field is required when :values are present.',
    'required_without' => ':attribute  फ़ील्ड की आवश्यकता होती है जब :values मौजूद नहीं होता है।',
    'required_without_all' => ':values मौजूद नहीं होने पर :attribute फ़ील्ड आवश्यक है।',
    'same' => ':other और :attribute को मेल खाना चाहिए।',
    'size' => [
        'numeric' => 'The :attribute must be :size.',
        'file' => 'The :attribute must be :size kilobytes.',
        'string' => 'The :attribute must be :size characters.',
        'array' => 'The :attribute must contain :size items.',
    ],
    'starts_with' => 'The :attribute must start with one of the following: :values',
    'string' => ':attribute एक स्ट्रिंग होनी चाहिए।',
    'timezone' => 'The :attribute must be a valid zone.',
    'unique' => 'यह :attribute पहले ही लिया जा चुका है।',
    'uploaded' => 'The :attribute failed to upload.',
    'url' => 'The :attribute format is invalid.',
    'uuid' => 'The :attribute must be a valid UUID.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'not_present_with' => 'The :attribute must not be present when :values is also present.',
    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
        'parent_id' => [
            'exists' => 'उस आईडी वाला एक समूह मौजूद नहीं है।',
            'valid_parent_id' => 'एक समूह स्वयं का पूर्वज नहीं हो सकता।',
            'unique' => 'पेरेंट आईडी को ऐसे समूह में सेट नहीं किया जा सकता है जिसमें उपयोगकर्ता होते हैं।'

        ],
        'amount' => [
            'min' => 'आप नकारात्मक राशि नहीं निकाल सकते!',
            'regex' => 'एक वैध मौद्रिक मूल्य होना चाहिए!'
        ],
        'users.*.group' => [
            'exists' => 'उस आईडी वाला एक समूह मौजूद नहीं है।',
            'unique' => 'एक समूह को उस समूह की आईडी पर सेट नहीं किया जा सकता है जिसमें बच्चे शामिल हैं।'
        ],
        'id' => [
            'unique' => 'उपयोगकर्ता को उस समूह में नहीं जोड़ा जा सकता है जिसमें उपसमूह शामिल हैं।'
        ],
        'user_id' => [
            'unique' => 'उपयोगकर्ता पहले से ही मौजूद है।'
        ],
        'users.*.email' => [
            'required_if' => 'आईडी खाली होने पर ईमेल की आवश्यकता होती है।',
            'valid_user_email' => 'ईमेल मौजूद नहीं है।',
            'valid_unique_email' => 'ईमेल अद्वितीय होना चाहिए।'  
        ],
        'users.*.id' => [
            'required_if' => 'ईमेल खाली होने पर आईडी की आवश्यकता होती है।'
        ],
        'users.*.external_id' => [
            'valid_user_external_id' => 'बाहरी आईडी पहले ही ली जा चुकी है।'
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'email' => 'ईमेल',
        'password' => 'पासवर्ड',
        'cpassword' => 'कन्फर्म पासपोर्ट',
        'username' => 'यूजर का नाम',
        'name' => 'नाम',
        'first_name' => 'पहला नाम',
        'last_name' => 'अंतिम नाम',
        'metadata' => 'मेटाडेटा',
        'parent_id' => 'पैरेंट आईडी',
        'is_default_group' => 'क्या डिफ़ॉल्ट समूह',
        'group_id'  => 'समूह आईडी',
        'backup_group_id'  => 'बैकअप समूह आईडी',
        'user_id'   => 'यूज़र आईडी',
        'external_id'    => 'बाहरी आईडी',
        'value' => 'वैल्यू',
        'timestamp' => 'समय-चिह्न',
        'weight'    => 'वजन',
        'league_id' => 'लीग आईडी',
        'subject' => 'विषय',
        'body' => 'बॉडी',
        'is_enabled' => 'सक्षम किया गया',
        'resend_interval'  => 'अंतराल को पुनः भेजें',
        'id'  => 'आईडी',
        'email_type'  => 'ईमेल प्रकार'
    ],

];
