<?php
return [

	'hero' => [
		'field_name' => [
			'title' => 'text',
			'button_name' => 'text',
			'button_link' => 'url',
			'video_link' => 'url',
		],
		'validation' => [
			'title.*' => 'required|min:2|max:1000',
			'button_name.*' => 'required|max:100',
			'button_link.*' => 'required|url',
			'video_link.*' => 'required|url',
		],
	],

	'feature' => [
		'field_name' => [
			'heading' => 'text',
			'title' => 'text',
			'short_description' => 'textarea',
		],
		'validation' => [
			'heading.*' => 'required|min:2|max:100',
			'title.*' => 'required|min:2|max:100',
			'short_description.*' => 'required|min:2|max:2000',
		],
	],

	'about-us' => [
		'field_name' => [
			'heading' => 'text',
			'title' => 'text',
			'button_name' => 'text',
			'button_link' => 'url',
			'image' => 'file',
			'short_description' => 'textarea',
		],
		'validation' => [
			'heading.*' => 'required|min:2|max:100',
			'title.*' => 'required|min:2|max:100',
			'button_name.*' => 'required|max:100',
			'button_link.*' => 'required|url',
			'image.*' => 'required|max:5072|image|mimes:jpg,jpeg,png',
			'short_description.*' => 'required|min:2|max:2000',
		],
		'size' => [
			'image' => '940x626'
		],
	],

	'how-it-work' => [
		'field_name' => [
			'heading' => 'text',
			'title' => 'text',
			'short_description' => 'textarea',
		],
		'validation' => [
			'heading.*' => 'required|min:2|max:1000',
			'title.*' => 'required|min:2|max:1000',
			'short_description.*' => 'required|min:2|max:20000',
		],
	],

	'why-choose-us' => [
		'field_name' => [
			'heading' => 'text',
			'title' => 'text',
			'image' => 'file',
		],
		'validation' => [
			'heading.*' => 'required|min:2|max:1000',
			'title.*' => 'required|min:2|max:1000',
			'image.*' => 'required|max:3072|image|mimes:jpg,jpeg,png',
		],
		'size' => [
			'image' => '940x626'
		],
	],


	'testimonial' => [
		'field_name' => [
			'heading' => 'text',
			'title' => 'text',
			'short_description' => 'textarea',
		],
		'validation' => [
			'heading.*' => 'required|min:2|max:1000',
			'title.*' => 'required|min:2|max:1000',
			'short_description.*' => 'required|min:2|max:20000',
		],
	],


	'faq' => [
		'field_name' => [
			'heading' => 'text',
			'title' => 'text',
			'image' => 'file',
			'short_description' => 'textarea',
		],
		'validation' => [
			'heading.*' => 'required|min:2|max:1000',
			'title.*' => 'required|min:2|max:1000',
			'image.*' => 'required|max:3072|image|mimes:jpg,jpeg,png',
			'short_description.*' => 'required|min:2|max:20000',
		],
		'size' => [
			'image' => '940x626'
		],
	],

	'blog' => [
		'field_name' => [
			'heading' => 'text',
			'title' => 'text',
			'short_description' => 'textarea',
		],
		'validation' => [
			'heading.*' => 'required|min:2|max:1000',
			'title.*' => 'required|min:2|max:1000',
			'short_description.*' => 'required|min:2|max:20000',
		],
	],

	'app-section' => [
		'field_name' => [
			'title' => 'text',
		],
		'validation' => [
			'title.*' => 'required|min:2|max:1000',
		],
	],

	'newsletter' => [
		'field_name' => [
			'title' => 'text',
		],
		'validation' => [
			'title.*' => 'required|min:2|max:1000',
		],
	],

	'contact' => [
		'field_name' => [
			'title' => 'text',
			'short_description' => 'textarea',
			'phone' => 'text',
			'email' => 'text',
			'location' => 'text',
			'about_company' => 'textarea',
		],
		'validation' => [
			'title.*' => 'required|min:2|max:1000',
			'short_description.*' => 'required|min:2|max:1000',
			'phone.*' => 'required|min:2|max:1000',
			'email.*' => 'required|min:2|max:1000',
			'location.*' => 'required|min:2|max:1000',
			'about_company.*' => 'required|min:2|max:10000',
		],
	],

	'login' => [
		'field_name' => [
			'title' => 'text',
			'sub_title' => 'text',
		],
		'validation' => [
			'title.*' => 'required|min:2|max:100',
			'sub_title.*' => 'required|min:2|max:150',
		],
	],

	'register' => [
		'field_name' => [
			'title' => 'text',
			'sub_title' => 'text',
		],
		'validation' => [
			'title.*' => 'required|min:2|max:100',
			'sub_title.*' => 'required|min:2|max:150',
		],
	],

	'forget-password' => [
		'field_name' => [
			'title' => 'text',
			'sub_title' => 'text',
		],
		'validation' => [
			'title.*' => 'required|min:2|max:100',
			'sub_title.*' => 'required|min:2|max:150',
		],
	],

	'reset-password' => [
		'field_name' => [
			'title' => 'text',
			'sub_title' => 'text',
		],
		'validation' => [
			'title.*' => 'required|min:2|max:100',
			'sub_title.*' => 'required|min:2|max:150',
		],
	],

	'email-verification' => [
		'field_name' => [
			'title' => 'text',
			'sub_title' => 'text',
		],
		'validation' => [
			'title.*' => 'required|min:2|max:100',
			'sub_title.*' => 'required|min:2|max:150',
		],
	],

	'sms-verification' => [
		'field_name' => [
			'title' => 'text',
			'sub_title' => 'text',
		],
		'validation' => [
			'title.*' => 'required|min:2|max:100',
			'sub_title.*' => 'required|min:2|max:150',
		],
	],

	'message' => [
		'required' => 'This field is required.',
		'min' => 'This field must be at least :min characters.',
		'max' => 'This field may not be greater than :max characters.',
		'image' => 'This field must be image.',
		'mimes' => 'This image must be a file of type: jpg, jpeg, png.',
	],

	'template_media' => [
		'image' => 'file',
		'top_image' => 'file',
		'bottom_image' => 'file',
		'button_link' => 'url',
		'video_link' => 'url'
	]
];
