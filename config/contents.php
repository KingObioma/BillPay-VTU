<?php
return [

	'feature' => [
		'field_name' => [
			'title' => 'text',
			'icon' => 'icon',
			'short_description' => 'textarea',
		],
		'validation' => [
			'title.*' => 'required|min:2|max:100',
			'icon.*' => 'required',
			'short_description.*' => 'required|min:2|max:2000',
		],
	],

	'how-it-work' => [
		'field_name' => [
			'title' => 'text',
			'icon' => 'icon',
			'short_description' => 'textarea',
		],
		'validation' => [
			'title.*' => 'required|min:2|max:100',
			'icon.*' => 'required',
			'short_description.*' => 'required|min:2|max:20000',
		],
	],

	'why-choose-us' => [
		'field_name' => [
			'title' => 'text',
			'short_description' => 'textarea',
		],
		'validation' => [
			'title.*' => 'required|min:2|max:1000',
			'short_description.*' => 'required|min:2|max:20000',
		],
	],

	'testimonial' => [
		'field_name' => [
			'name' => 'text',
			'address' => 'text',
			'image' => 'file',
			'short_description' => 'textarea',
		],
		'validation' => [
			'title.*' => 'required|min:2|max:1000',
			'address.*' => 'required|min:2|max:1000',
			'image.*' => 'required|max:3072|image|mimes:jpg,jpeg,png',
			'short_description.*' => 'required|min:2|max:20000',
		],
		'size' => [
			'image' => '500x480'
		]
	],

	'faq' => [
		'field_name' => [
			'title' => 'text',
			'short_description' => 'textarea',
		],
		'validation' => [
			'title.*' => 'required|min:2|max:100',
			'short_description.*' => 'required|min:2|max:2000',
		],
	],

	'blog' => [
		'field_name' => [
			'writer_name' => 'text',
			'writer_designation' => 'text',
			'writer_image' => 'file',
			'title' => 'text',
			'image' => 'file',
			'description' => 'textarea',
		],
		'validation' => [
			'writer_name.*' => 'required|min:2|max:100',
			'writer_designation.*' => 'required|min:2|max:500',
			'writer_image.*' => 'nullable|max:3072|image|mimes:jpg,jpeg,png',
			'title.*' => 'required|min:2|max:100',
			'image.*' => 'nullable|max:3072|image|mimes:jpg,jpeg,png',
			'description.*' => 'required|min:2|max:10000',
		],
	],

	'app-section' => [
		'field_name' => [
			'title' => 'text',
			'icon' => 'icon',
			'button_link' => 'url',
		],
		'validation' => [
			'title.*' => 'required',
			'icon.*' => 'required',
			'button_link.*' => 'required|url',
		],
	],

	'social' => [
		'field_name' => [
			'social_icon' => 'icon',
			'social_link' => 'url',
		],
		'validation' => [
			'social_icon.*' => 'required',
			'social_link.*' => 'required|url',
		],
	],

	'pages' => [
		'field_name' => [
			'title' => 'text',
			'description' => 'textarea'
		],
		'validation' => [
			'title.*' => 'required|max:100',
			'description.*' => 'required|max:100000'
		]
	],

	'message' => [
		'required' => 'This field is required.',
		'min' => 'This field must be at least :min characters.',
		'max' => 'This field may not be greater than :max characters.',
		'image' => 'This field must be image.',
		'mimes' => 'This image must be a file of type: jpg, jpeg, png.',
	],

	'content_media' => [
		'image' => 'file',
		'writer_image' => 'file',
		'thumbnail' => 'file',
		'youtube_link' => 'url',
		'social_icon' => 'icon',
		'icon' => 'icon',
		'social_link' => 'url',
		'button_link' => 'url'
	]
];
