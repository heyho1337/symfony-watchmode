// public/js/AddToFavourites.js

function submitForm(event) {
	event.preventDefault();
	//const form = document.querySelector('form[name="remove_from_favourite"]');
	const form = event.target.closest('form');
	const formData = new FormData(form);
	const routePath = form.action;
	fetch(routePath, {
		method: 'POST',
		body: formData
	})
	.then(response => response.json())
	.then(data => {
		form.parentElement.remove();
		$('body').append(data.result)
	})
	.catch(error => {
		console.error('Error:', error);
	});
}
