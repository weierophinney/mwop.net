(function () {
  const btn = document.querySelector("button.mobile-menu-button");
  const menu = document.querySelector(".mobile-menu");

  btn.addEventListener("click", () => {
    menu.classList.toggle("hidden");
  });
})();

(function () {
  'use strict';

  const searchElements = document.querySelectorAll('.search');

  const headers = new Headers();
  headers.append('Accept', 'application/json');

  const search = (query, callback) => {
    const queryString = new URLSearchParams('');
    queryString.set('q', query);

    const url = '/search?' + queryString.toString();

    const data = {
      method: 'GET',
      headers: headers,
      mode: 'cors',
      cache: 'default'
    };

    fetch(url, data)
      .then((response) => {
        if (! response.ok) {
          throw new Error('Invalid response from search endpoint');
        }
        return response.json();
      })
      .then((payload) => {
        callback(payload);
      });
  };

  searchElements.forEach((searchElement) => {
    const autocomplete = searchElement.querySelector('input');
    const resultList = searchElement.querySelector('.search-results');

    autocomplete.oninput = function () {
      let results = [];
      const userInput = this.value;
      resultList.innerHTML = "";

      if (userInput.length > 0) {
        search(userInput, (results) => {
          for (let i = 0; i < results.length; i++) {
            resultList.innerHTML += `<li class="search-result"><a href="${ results[i].link }">${ results[i].title }</a></li>`;
          }
        });
      }
    };
  });
})();
