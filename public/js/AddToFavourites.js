// public/js/AddToFavourites.js

function submitForm(event) {
	event.preventDefault();
	//const form = document.querySelector('form[name="add_to_favourite"]');
	const form = event.target.closest('form');
	console.log(event.target);
	const formData = new FormData(form);
	const routePath = form.action;

	fetch(routePath, {
		method: 'POST',
		body: formData
	})
	.then(response => response.json())
	.then(data => {
		console.log(data);
		$('body').append(data.result)
	})
	.catch(error => {
		console.error('Error:', error);
	});
}
