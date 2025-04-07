function openModal(element) {
    const modal = document.getElementById('imageModal');
    const modalImg = document.getElementById('modalImg');
    modal.style.display = 'block';
    modalImg.src = element.querySelector('img').src; // Get the image source
}

function closeModal() {
    const modal = document.getElementById('imageModal');
    modal.style.display = 'none';
}