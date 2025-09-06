class Dropdown {

  search(query) {
    if (query.length < 3) {
    document.querySelector(`.${query}`).innerHTML = '';
    return;
    }
  }

  response(query) {
    fetch('search.php?q=' + encodeURIComponent(query))
      .then(response => response.json())
      .then(data => {
        let itemsHTML = '';
        data.forEach(item => {
          itemsHTML += ``;
        });
      })

  }
    
}
