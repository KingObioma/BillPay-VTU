// Preloader area
var preloader = document.getElementById("preloader");

function preloder_function() {
	preloader.style.display = "none";
}

// toggleSideMenu start
const toggleSideMenu = () => {
	document.body.classList.toggle("toggle-sidebar");
};
// toggleSideMenu end


(function () {
	"use strict";

	/**
	 * Easy selector helper function
	 */
	const select = (el, all = false) => {
		el = el.trim()
		if (all) {
			return [...document.querySelectorAll(el)]
		} else {
			return document.querySelector(el)
		}
	}

	/**
	 * Easy event listener function
	 */
	const on = (type, el, listener, all = false) => {
		if (all) {
			select(el, all).forEach(e => e.addEventListener(type, listener))
		} else {
			select(el, all).addEventListener(type, listener)
		}
	}

	/**
	 * Easy on scroll event listener
	 */
	const onscroll = (el, listener) => {
		el.addEventListener('scroll', listener)
	}

	/**
	 * Sidebar toggle
	 */
	// if (select('.toggle-sidebar-btn')) {
	//     on('click', '.toggle-sidebar-btn', function (e) {
	//         select('body').classList.toggle('toggle-sidebar')
	//     })
	// }


	// //  Search bar toggle

	if (select('.search-bar-toggle')) {
		on('click', '.search-bar-toggle', function (e) {
			select('.search-bar').classList.toggle('search-bar-show')
		})
	}

	/**
	 * Navbar links active state on scroll
	 */
	let navbarlinks = select('#navbar .scrollto', true)
	const navbarlinksActive = () => {
		let position = window.scrollY + 200
		navbarlinks.forEach(navbarlink => {
			if (!navbarlink.hash) return
			let section = select(navbarlink.hash)
			if (!section) return
			if (position >= section.offsetTop && position <= (section.offsetTop + section.offsetHeight)) {
				navbarlink.classList.add('active')
			} else {
				navbarlink.classList.remove('active')
			}
		})
	}
	window.addEventListener('load', navbarlinksActive)
	onscroll(document, navbarlinksActive)

	/**
	 * Toggle .header-scrolled class to #header when page is scrolled
	 */
	let selectHeader = select('#header')
	if (selectHeader) {
		const headerScrolled = () => {
			if (window.scrollY > 100) {
				selectHeader.classList.add('header-scrolled')
			} else {
				selectHeader.classList.remove('header-scrolled')
			}
		}
		window.addEventListener('load', headerScrolled)
		onscroll(document, headerScrolled)
	}

	/**
	 * Back to top button
	 */
	let backtotop = select('.back-to-top')
	if (backtotop) {
		const toggleBacktotop = () => {
			if (window.scrollY > 100) {
				backtotop.classList.add('active')
			} else {
				backtotop.classList.remove('active')
			}
		}
		window.addEventListener('load', toggleBacktotop)
		onscroll(document, toggleBacktotop)
	}

	/**
	 * Initiate tooltips
	 */
	var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
	var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
		return new bootstrap.Tooltip(tooltipTriggerEl)
	})

	/**
	 * Initiate quill editors
	 */
	if (select('.quill-editor-default')) {
		new Quill('.quill-editor-default', {
			theme: 'snow'
		});
	}

	if (select('.quill-editor-bubble')) {
		new Quill('.quill-editor-bubble', {
			theme: 'bubble'
		});
	}

	if (select('.quill-editor-full')) {
		new Quill(".quill-editor-full", {
			modules: {
				toolbar: [
					[{
						font: []
					}, {
						size: []
					}],
					["bold", "italic", "underline", "strike"],
					[{
						color: []
					},
						{
							background: []
						}
					],
					[{
						script: "super"
					},
						{
							script: "sub"
						}
					],
					[{
						list: "ordered"
					},
						{
							list: "bullet"
						},
						{
							indent: "-1"
						},
						{
							indent: "+1"
						}
					],
					["direction", {
						align: []
					}],
					["link", "image", "video"],
					["clean"]
				]
			},
			theme: "snow"
		});
	}

	/**
	 * Initiate TinyMCE Editor
	 */
	const useDarkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
	const isSmallScreen = window.matchMedia('(max-width: 1023.5px)').matches;


	/**
	 * Initiate Bootstrap validation check
	 */
	var needsValidation = document.querySelectorAll('.needs-validation')

	Array.prototype.slice.call(needsValidation)
		.forEach(function (form) {
			form.addEventListener('submit', function (event) {
				if (!form.checkValidity()) {
					event.preventDefault()
					event.stopPropagation()
				}

				form.classList.add('was-validated')
			}, false)
		})

	/**
	 * Initiate Datatables
	 */
	const datatables = select('.datatable', true)
	datatables.forEach(datatable => {
		new simpleDatatables.DataTable(datatable);
	})

	/**
	 * Autoresize echart charts
	 */
	const mainContainer = select('#main');
	if (mainContainer) {
		setTimeout(() => {
			new ResizeObserver(function () {
				select('.echart', true).forEach(getEchart => {
					echarts.getInstanceByDom(getEchart).resize();
				})
			}).observe(mainContainer);
		}, 200);
	}

})();


// input file preview
const previewImage = (id) => {
	document.getElementById(id).src = URL.createObjectURL(event.target.files[0]);
};

// Tooltip
const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));


// cmn-select2
$(document).ready(function () {
	$('.cmn-select2-dropdown').select2();
});
$(".modal-select").select2({
	dropdownParent: $("#formModal"),
});

// select2 with image
function formatState(state) {
	if (!state.id) {
		return state.text;
	}
	var baseUrl = "../img/mini-flag";
	var $state = $(
		'<span><img src="' + baseUrl + '/' + state.element.value.toLowerCase() + '.svg" class="img-flag" /> ' + state.text + '</span>'
	);
	return $state;
};
// select2
$(".js-example-basic-single").select2({
	templateResult: formatState
});


$(document).ready(function () {
	// owl carousel dashboard card
	$('.carousel-1').owlCarousel({
		loop: true,
		autoplay: false,
		margin: -20,
		nav: false,
		dots: false,
		// rtl:true,
		responsive: {
			0: {
				items: 1
			},
			576: {
				items: 2
			},
			768: {
				items: 3
			}
		}
	});


});


// Dark theme start
const toggleBtn = document.getElementById("toggle-btn");
const body = document.querySelector("body");
toggleBtn.addEventListener("click", function () {
	document.body.classList.toggle("dark-theme");
	if (document.body.classList.contains("dark-theme")) {
		localStorage.setItem("dark-theme", 1);
	} else {
		localStorage.setItem("dark-theme", 0);
	}
	setTheme();
});

function setTheme() {
	const isDarkTheme = localStorage.getItem("dark-theme");
	if (isDarkTheme == 1) {
		document.querySelector('body').classList.add('dark-theme');
		document.getElementById("moon").style.display = "none";
		document.getElementById("sun").style.display = "block";
	} else {
		document.querySelector('body').classList.remove('dark-theme');
		document.getElementById("moon").style.display = "block";
		document.getElementById("sun").style.display = "none";
	}
}

setTheme();
// Dark theme end

