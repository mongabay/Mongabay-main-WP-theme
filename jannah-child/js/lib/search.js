let controller;
const domain = window.location.origin;
let selectedTopics = [];
let selectedLocations = [];
const searchInput = document.getElementById("searchInput");
const defaultSuggestions = document.getElementById("default");
const noResults = document.getElementById("no-results");
const formatResults = document.getElementById("formats-results");
const results = document.getElementById("results");
let isResultsLoading = false;

let totalCount;
let selectedFormat = "posts";
let cursor = "";

const articlesData = {
  0: ["Forests"],
  1: ["Wildlife"],
  2: ["Oceans"],
  3: ["Feature"],
};

const topicsData = {
  0: ["Animals", "animals"],
  1: ["Forests", "forests"],
  2: ["Oceans", "oceans"],
  3: ["Conservation", "conservation"],
  4: ["Indigenous Peoples", "indigenous-peoples"],
};

const locationsData = {
  0: ["Asia", "asia"],
  1: ["Africa", "africa"],
  2: ["South America", "south-america"],
  3: ["Indonesia", "indonesia"],
  4: ["Amazon", "amazon"],
  5: ["Congo", "congo"],
};

const clearAllTagsText = "Clear all tags";
const clearSearchInputText = "Clear search input";

const formatOptions = {
  posts: ["Posts", "posts"],
  // customStories: ["Custom Stories", "customStories"],
  shortArticles: ["Short", "shortArticles"],
  videos: ["Video", "videos"],
  podcasts: ["Podcast", "podcasts"],
  specialsArticles: ["Special", "specialsArticles"],
};

function createDefaultList(type, data) {
  const output = document.getElementById(`${type}-suggestions`);

  Object.values(data).forEach((item) => {
    if (item[1] === "posts") {
      return;
    }
    const itemSpan = document.createElement("span");
    itemSpan.textContent = item[0];

    itemSpan.addEventListener("click", () => {
      if (type === "articles") {
        searchInput.value = item[0];
        fetchArticles(true);
      } else {
        if (type === "topics") {
          if (!selectedTopics.includes(item[1])) {
            createTaxTag("topic", item[0], item[1]);

            if (searchInput.value.length > 0) {
              // fetchArticles(true);
            }
            fetchArticles(true);
            // clearSearch();
          }
        }

        if (type === "locations") {
          if (!selectedLocations.includes(item[1])) {
            createTaxTag("location", item[0], item[1]);

            if (searchInput.value.length > 0) {
              // fetchArticles(true);
            }
            fetchArticles(true);
            // clearSearch();
          }
        }

        if (type === "formats") {
          if (selectedFormat !== item[1]) {
            createFormatTag(item[0], item[1], true);

            if (searchInput.value.length > 0) {
              // fetchArticles(true);
            }
            fetchArticles(true);
            // clearSearch();
          }
        }
      }
    });

    output.appendChild(itemSpan);
  });

  return output;
}

createDefaultList("articles", articlesData);
createDefaultList("topics", topicsData);
createDefaultList("locations", locationsData);
createDefaultList("formats", formatOptions);

function isValueString(value) {
  return typeof value === "string" || value instanceof String;
}

const queriedSearch = window.location.search;

const queriedParams = queriedSearch.split("&").reduce((acc, param) => {
  const [key, value] = param.split("=");
  if (key === "?s" && value.length > 0) {
    acc["search"] = decodeURI(value);
  } else {
    if (key.length > 0 && value.length > 0) {
      acc[key] = value && value.includes("+") ? value.split("+") : value;
    }
  }
  return acc;
}, {});

if (Object.keys(queriedParams).length > 0) {
  if (queriedParams.search) {
    document.getElementById("searchInput").value = queriedParams.search;
    displayClearButton("#searchInput", ".search-actions", clearSearchInputText);
  }

  if (queriedParams.locations) {
    selectedLocations = isValueString(queriedParams.locations)
      ? [queriedParams.locations]
      : queriedParams.locations;

    if (isValueString(queriedParams.locations)) {
      createTaxTag("location", queriedParams.locations, queriedParams.locations);
    } else {
      queriedParams.locations.forEach((location) => {
        createTaxTag("location", location, location);
      });
    }
  }
  if (queriedParams.topics) {
    selectedTopics = isValueString(queriedParams.topics)
      ? [queriedParams.topics]
      : queriedParams.topics;
    if (isValueString(queriedParams.topics)) {
      createTaxTag("topic", queriedParams.topics, queriedParams.topics);
    } else {
      queriedParams.topics.forEach((topic) => {
        createTaxTag("topic", topic, topic);
      });
    }
  }
  if (queriedParams.format) {
    selectedFormat = queriedParams.format;
    const formatName = formatOptions[queriedParams.format][0];
    const formatSlug = formatOptions[queriedParams.format][1];

    if (formatName && formatSlug) {
      createFormatTag(formatName, formatSlug, true);
    }
  }

  fetchArticles(true);
}

function formatDate(date) {
  const dateString = new Date(date).toDateString();
  dateArray = dateString.split(" ");
  return `${dateArray[1]} ${dateArray[2]}, ${dateArray[3]}`;
}

/**
 * Shows animated preloader.
 * @param {boolean} show.
 */
function preloader(show) {
  return show
    ? `
        <div class="preloader-wrapper">
          <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24"><g stroke="currentColor"><circle cx="12" cy="12" r="9.5" fill="none" stroke-linecap="round" stroke-width="3"><animate attributeName="stroke-dasharray" calcMode="spline" dur="1.5s" keySplines="0.42,0,0.58,1;0.42,0,0.58,1;0.42,0,0.58,1" keyTimes="0;0.475;0.95;1" repeatCount="indefinite" values="0 150;42 150;42 150;42 150"/><animate attributeName="stroke-dashoffset" calcMode="spline" dur="1.5s" keySplines="0.42,0,0.58,1;0.42,0,0.58,1;0.42,0,0.58,1" keyTimes="0;0.475;0.95;1" repeatCount="indefinite" values="0;-16;-59;-59"/></circle><animateTransform attributeName="transform" dur="2s" repeatCount="indefinite" type="rotate" values="0 12 12;360 12 12"/></g></svg>
        </div>`
    : null;
}

/**
 * Debounce function.
 * @param {function} func - function to pass which should be delayed.
 * @param {number} delay - delay in milliseconds.
 */
function debounce(func, delay) {
  let timer;
  return function () {
    const context = this;
    const args = arguments;
    clearTimeout(timer);
    timer = setTimeout(() => {
      func.apply(context, args);
    }, delay);
  };
}

const hasFilters = selectedTopics.length || selectedLocations.lengt;

function filtersCheck() {
  if (
    !searchInput.value.length &&
    !selectedTopics.length &&
    !selectedLocations.length &&
    selectedFormat === "posts"
  ) {
    document.getElementById("results").classList.add("hide");
    document.querySelector(".results-footer").classList.add("hide");
    defaultSuggestions.classList.remove("hide");
    noResults.classList.add("hide");
  }
}

/**
 * Clears the selected tags based on the type.
 * @param {string} type - The type of tags to clear ("topics", "locations" or "formats").
 */
function clearTags(type) {
  if (type === "topics") {
    selectedTopics = [];
    const topicsResults = document.querySelector("#topics-results");

    document.querySelectorAll(".tax-item-wrapper.topics button.tax-tag-topic").forEach((tag) => {
      tag.remove();
    });
    topicsResults.innerHTML = "";
    topicsResults.classList.add("hide");
    document.querySelector(".tax-search-actions.topic").innerHTML = "";
  }

  if (type === "locations") {
    selectedLocations = [];
    const locationsResults = document.querySelector("#locations-results");

    document
      .querySelectorAll(".tax-item-wrapper.locations button.tax-tag-location")
      .forEach((tag) => {
        tag.remove();
      });

    locationsResults.innerHTML = "";
    locationsResults.classList.add("hide");
    document.querySelector(".tax-search-actions.location").innerHTML = "";
  }

  if (type === "formats") {
    selectedFormat = "post";
    const formatsResults = document.querySelector("#formats-results");
    formatsResults.innerHTML = "";
    formatsResults.classList.add("hide");
  }

  filtersCheck();
}

function clearSearch() {
  searchInput.value = "";
  document.getElementById("results").classList.add("hide");
  document.querySelector(".results-footer").classList.add("hide");
  defaultSuggestions.classList.remove("hide");
  noResults.classList.add("hide");

  if (document.querySelector(".search-input-wrapper button.clear-button")) {
    document.querySelector(".search-input-wrapper button.clear-button").remove();
  }

  if (hasFilters) {
    fetchArticles(true);
  }
}

const shouldFetch =
  searchInput.value.length > 0 || selectedTopics.length > 0 || selectedLocations.length > 0;

/**
 * Creates clear button for input fields
 * @param {string} insertAfterElement - insert after html selector tag.
 * @param {string} insertLocation - optional insert location as html selector tag.
 */
function displayClearButton(inputTag, insertLocation = "", toolTipText = "") {
  const input = document.querySelector(inputTag);
  const searchInput = document.getElementById("searchInput");

  function renderClearButton() {
    const clearButton = document.createElement("button");
    clearButton.classList.add("clear-button");

    const toolTipWrapper = document.createElement("div");
    toolTipWrapper.classList.add("extra-tooltip");

    const toolTip = document.createElement("span");
    toolTip.classList.add("tooltiptext");

    toolTip.textContent = toolTipText;

    toolTipWrapper.appendChild(toolTip);
    toolTipWrapper.appendChild(clearButton);

    clearButton.addEventListener("click", () => {
      clearButton.remove();

      if (inputTag === "#searchInput") {
        clearSearch();
        filtersCheck();
      }
      if (inputTag === "#searchTopic") {
        clearTags("topics");
        filtersCheck();
        // clearSearch();
      }
      if (inputTag === "#searchLocation") {
        clearTags("locations");
        filtersCheck();
        // clearSearch();
      }
      if (inputTag === "#searchFormat") {
        clearTags("formats");
        filtersCheck();
        // clearSearch();
      }

      if (searchInput.value.length) {
        // fetchArticles(true);
      }

      if (shouldFetch) {
        fetchArticles(true);
      }
    });

    return toolTipText.length > 0 ? toolTipWrapper : clearButton;
  }

  if (
    input.value.length > 0 &&
    !input.nextSibling.nodeName.includes("BUTTON") &&
    !insertLocation.length
  ) {
    input.insertAdjacentElement("afterend", renderClearButton());
  }

  if (insertLocation.length && !document.querySelector(`${insertLocation} .clear-button`)) {
    document.querySelector(insertLocation).appendChild(renderClearButton());
  }
}

/**
 * * @param {'topic' | 'location'} type - Either topic or location.
 * @param {string} name - The name of the tag.
 * @param {string} slug - The slug of the tag.
 * @param {boolean} showResults - Whether to show the results or not.
 */
function createTaxTag(type, name, slug, showResults = false) {
  const tag = document.createElement("button");
  tag.classList.add(`tax-tag-${type}`);

  if (type === "topic" && !selectedTopics.includes(slug)) {
    selectedTopics.push(slug);

    if (
      selectedTopics.length > 0 &&
      !document.querySelector(".tax-search-actions.topic .clear-button")
    ) {
      displayClearButton("#searchTopic", ".tax-search-actions.topic", clearAllTagsText);
    }
  }

  if (type === "location" && !selectedLocations.includes(slug)) {
    selectedLocations.push(slug);

    if (
      selectedLocations.length > 0 &&
      !document.querySelector(".tax-search-actions.location .clear-button")
    ) {
      displayClearButton("#searchLocation", ".tax-search-actions.location", clearAllTagsText);
    }
  }

  tag.textContent = name.replace(/-/g, " ");

  const searchSelectorId = type === "topic" ? "searchTopic" : "searchLocation";

  document
    .querySelector(`.tax-item-wrapper.${type}s`)
    .insertBefore(tag, document.getElementById(searchSelectorId));

  if (showResults) {
    fetchArticles();
  }

  tag.addEventListener("click", () => {
    if (type === "topic") {
      selectedTopics.splice(selectedTopics.indexOf(slug), 1);

      if (
        selectedTopics.length === 0 &&
        document.querySelector(`.tax-search-actions.${type} .clear-button`)
      ) {
        document.querySelector(`.tax-search-actions.${type} .clear-button`).remove();
        document.querySelector(`.tax-search-actions.${type}`).innerHTML = "";
      }
    } else {
      selectedLocations.splice(selectedLocations.indexOf(slug), 1);

      if (
        selectedLocations.length === 0 &&
        document.querySelector(`.tax-search-actions.${type} .clear-button`)
      ) {
        document.querySelector(`.tax-search-actions.${type} .clear-button`).remove();
        document.querySelector(`.tax-search-actions.${type}`).innerHTML = "";
      }
    }
    document.querySelector(`.tax-item-wrapper.${type}s`).removeChild(tag);

    filtersCheck();
    fetchArticles(true);
  });

  document.getElementById(searchSelectorId).value = "";
}

if (selectedTopics.length > 0) {
  displayClearButton("#searchTopic", ".tax-search-actions.topic", clearAllTagsText);
}
/**
 * * @param {string} name - The name of the format.
 * @param {string} slug - The slug of the format.
 * @param {boolean} showResults - Whether to show the results or not.
 */

function createFormatTag(name, slug, showResults = false) {
  if (slug === "posts") {
    return;
  }

  const formatResults = document.getElementById("formats-results");
  const formatTag = document.createElement("button");
  formatTag.textContent = name;

  if (document.querySelector(".tax-item-wrapper.format button")) {
    document.querySelector(".tax-item-wrapper.format button").remove();
  }

  document
    .querySelector(".tax-item-wrapper.format")
    .insertBefore(formatTag, document.getElementById("searchFormat"));
  selectedFormat = slug;

  if (document.querySelector(".tax-search-actions.format .clear-button")) {
    document.querySelector(".tax-search-actions.format .clear-button").remove();
  }

  formatTag.addEventListener("click", () => {
    selectedFormat = "posts";

    if (document.querySelector(".tax-item-wrapper.format button")) {
      document.querySelector(".tax-item-wrapper.format").removeChild(formatTag);
      filtersCheck();

      if (shouldFetch) {
        fetchArticles(true);
      }
    }
  });

  if (showResults) {
    formatResults.classList.add("hide");
  }
  document.getElementById("searchFormat").value = "";
}

function highlightResults(search, item) {
  const regex = new RegExp(search, "gi");

  let text = item.innerHTML;
  text = text.replace(/(<mark class="highlight">|<\/mark>)/gim, "");

  const newText = text.replace(regex, '<mark class="highlight">$&</mark>');
  item.innerHTML = newText;
}

// Various event listenrers to fetch entities and display clear input button
document.getElementById("searchInput").addEventListener(
  "input",
  debounce(function () {
    if (searchInput.value.length > 0) {
      fetchArticles();
      displayClearButton("#searchInput", ".search-actions", clearSearchInputText);
      document.getElementById("articles-suggestions").classList.add("hide");
    } else {
      document.getElementById("articles-suggestions").classList.remove("hide");
      document.getElementById("results").classList.add("hide");
      document.getElementById("default").classList.remove("hide");

      if (document.querySelector(".search-input-wrapper button.clear-button")) {
        document.querySelector(".search-input-wrapper button.clear-button").remove();
      }
    }
  }, 600),
);

document.getElementById("searchTopic").addEventListener("input", debounce(searchTopic, 500));

document.getElementById("searchLocation").addEventListener("input", debounce(searchLocation, 500));

document.getElementById("searchFormat").addEventListener("input", debounce(formatFilter, 500));

document.addEventListener("keydown", (e) => {
  if (e.key === "Escape") {
    const searchResultIds = [
      "topics-results",
      "locations-results",
      "formats-results",
      "topics-suggestions",
      "locations-suggestions",
      "articles-suggestions",
      "formats-suggestions",
    ];

    searchResultIds.forEach((id) => {
      document.getElementById(id).classList.add("hide");
    });
  }
});

// hide search results when clicked outside
document.addEventListener("click", (e) => {
  const searchResultIds = [
    "topics-results",
    "locations-results",
    "formats-results",
    "topics-suggestions",
    "locations-suggestions",
    "articles-suggestions",
    "formats-suggestions",
  ];

  if (!searchResultIds.includes(e.target.id)) {
    searchResultIds.forEach((id) => {
      if (
        e.target.id === "searchTopic" ||
        e.target.id === "searchLocation" ||
        e.target.id === "searchInput" ||
        e.target.id === "searchFormat"
      ) {
        return;
      }

      document.getElementById(id).classList.add("hide");
    });
  }
});

document.getElementById("searchInput").addEventListener("focus", () => {
  document.getElementById("articles-suggestions").classList.remove("hide");

  document.querySelectorAll(".dropdown").forEach((dropdown) => {
    if (dropdown.id !== "articles-suggestions") {
      dropdown.classList.add("hide");
    }
  });
});

document.getElementById("searchTopic").addEventListener("focus", () => {
  const suggestionsTopic = document.getElementById("topics-suggestions");
  suggestionsTopic.classList.remove("hide");

  document.querySelectorAll(".dropdown").forEach((dropdown) => {
    if (dropdown.id !== "topics-suggestions") {
      dropdown.classList.add("hide");
    }
  });

  suggestionsTopic.style.top = `${
    document.querySelector(".tax-item-wrapper.topics").offsetHeight
  }px`;
});

document.getElementById("searchLocation").addEventListener("focus", () => {
  const suggestionsLocation = document.getElementById("locations-suggestions");
  suggestionsLocation.classList.remove("hide");

  document.querySelectorAll(".dropdown").forEach((dropdown) => {
    if (dropdown.id !== "locations-suggestions") {
      dropdown.classList.add("hide");
    }
  });

  suggestionsLocation.style.top = `${
    document.querySelector(".tax-item-wrapper.locations").offsetHeight
  }px`;
});

document.getElementById("searchFormat").addEventListener("focus", () => {
  const suggestionsFormat = document.getElementById("formats-suggestions");
  suggestionsFormat.classList.remove("hide");

  document.querySelectorAll(".dropdown").forEach((dropdown) => {
    if (dropdown.id !== "formats-suggestions") {
      dropdown.classList.add("hide");
    }
  });

  suggestionsFormat.style.top = `${
    document.querySelector(".tax-item-wrapper.format").offsetHeight
  }px`;
});

/**
 * Fetch articles based on search criteria.
 * @param {boolean} fromStart - Whether to fetch articles from the start or not.
 */

async function fetchArticles(fromStart = false) {
  if (controller) {
    controller.abort();
  }

  controller = new AbortController();
  const signal = controller.signal;

  isResultsLoading = true;
  const searchInput = document.getElementById("searchInput");
  const resultsFooter = document.querySelector(".results-footer");
  const searchValue = searchInput.value;

  const previousSearchValue = searchInput.dataset.previousValue || "";
  searchInput.dataset.previousValue = searchValue;

  const loadModeButton = document.querySelector(".load-more");

  const clearResults = cursor.length === 0 || previousSearchValue !== searchValue || fromStart;

  if (clearResults) {
    results.innerHTML = "";
    cursor = "";
  }

  defaultSuggestions.classList.add("hide");
  resultsFooter.classList.remove("hide");
  resultsFooter.innerHTML = preloader(true);

  if (loadModeButton) {
    resultsFooter.innerHTML = preloader(true);
  }

  // if (!searchValue.length) {
  //   results.innerHTML = "";
  //   isResultsLoading = false;
  //   // return;
  // }

  const searchString = `?s=${searchValue}${
    selectedLocations.length ? `&locations=${selectedLocations.join("+")}` : ""
  }${selectedTopics.length ? `&topics=${selectedTopics.join("+")}` : ""}${
    selectedFormat ? `&format=${selectedFormat}` : ""
  }`;

  const nextURL = `${domain}${searchString}`;
  const nextTitle = `Searched for ${searchValue}`;
  const nextState = { additionalInformation: "New search performed" };

  window.history.pushState(nextState, nextTitle, nextURL);

  const topicGql = `{taxonomy:TOPIC,terms:${JSON.stringify(
    selectedTopics,
  )},field:SLUG,operator:IN}`;
  const locationGql = `{taxonomy:LOCATION,terms:${JSON.stringify(
    selectedLocations,
  )},field:SLUG,operator:IN}`;
  // const formatsGql = `{taxonomy:ARTICLEFORMATTYPE,terms:${JSON.stringify(
  //   selectedFormats,
  // )},field:SLUG,operator:IN}`;

  const taxArray = [];

  if (selectedTopics.length) {
    taxArray.push(topicGql);
  }
  if (selectedLocations.length) {
    taxArray.push(locationGql);
  }
  // if (selectedFormats.length) {
  // taxArray.push(formatsGql);
  // }

  const keyWordQuery = `, search:\"${searchValue}\"`;
  const taxQuery = `taxQuery:{taxArray:[${taxArray.join(",")}]}`;
  const paginate = `,after:\"${cursor}\"`;

  const resp = await fetch(
    `${domain}/graphql?query=query{${selectedFormat}(first:8${
      cursor.length ? paginate : ""
    },where:{status: PUBLISH${searchValue.length ? keyWordQuery : ""},${
      taxArray.length > 0 ? taxQuery : ""
    }}){edges{node{title,link,date,byline{nodes{name}},featuredImage{node{srcSet sizes(size: THUMBNAIL)}}}}pageInfo{endCursor hasNextPage hasPreviousPage startCursor total}}}`,
    {
      method: "GET",
      mode: "cors",
      cache: "no-cache",
      credentials: "same-origin",
      headers: {
        "Content-Type": "application/json",
      },
      redirect: "follow",
      referrerPolicy: "no-referrer",
    },
    { signal },
  );

  if (signal.aborted) {
    return; // Abort the fetch if the signal is true
  }

  const { errors, data } = await resp.json();

  if (errors) {
    resultsFooter.innerHTML = preloader(false);
    defaultSuggestions.classList.add("hide");

    noResults.classList.remove("hide");
    return;
  }

  if (clearResults) {
    const resultsList = document.createElement("div");
    resultsList.id = "post-results";
    resultsList.classList.add("grid-view");
    const listViewButton = document.createElement("button");
    listViewButton.id = "list-view";
    listViewButton.classList.add("demo-icon");
    listViewButton.textContent = "L";
    const gridViewButton = document.createElement("button");
    gridViewButton.id = "grid-view";
    gridViewButton.classList.add("active");
    gridViewButton.textContent = "G";

    const resultsTotal = document.createElement("div");
    const resultsHeader = document.createElement("div");
    const resultsViewToggles = document.createElement("div");
    const resultsHeaderLeft = document.createElement("div");
    const resultsRSS = document.createElement("a");
    resultsRSS.textContent = "RSS";

    const hasTopics = selectedTopics.length > 0;
    const hasLocations = selectedLocations.length > 0;

    resultsRSS.href = `${domain}/?feed=custom-rss-feed&s=${searchValue}&post_type=${selectedFormat}${
      hasTopics ? `&topic=${selectedTopics.join(",")}` : ""
    }${hasLocations ? `&location=${selectedLocations.join(",")}` : ""}`;
    resultsRSS.target = "_blank";

    resultsHeader.id = "results-header";
    resultsViewToggles.id = "results-view-toggles";
    resultsTotal.id = "results-total";
    resultsRSS.id = "results-rss";
    resultsHeaderLeft.id = "results-header-left";

    resultsHeaderLeft.appendChild(resultsRSS);
    resultsHeaderLeft.appendChild(resultsTotal);

    resultsHeader.appendChild(resultsHeaderLeft);
    resultsHeader.appendChild(resultsViewToggles);
    resultsViewToggles.appendChild(listViewButton);
    resultsViewToggles.appendChild(gridViewButton);
    results.appendChild(resultsHeader);
    results.appendChild(resultsList);

    gridViewButton.addEventListener("click", () => {
      resultsList.classList.remove("list-view");
      resultsList.classList.add("grid-view");
      gridViewButton.classList.add("active");
      listViewButton.classList.remove("active");
    });

    listViewButton.addEventListener("click", () => {
      resultsList.classList.remove("grid-view");
      resultsList.classList.add("list-view");
      listViewButton.classList.add("active");
      gridViewButton.classList.remove("active");
    });
  }

  const foundResultsCount = data[selectedFormat].edges.length;

  if (foundResultsCount === 0) {
    resultsFooter.innerHTML = preloader(false);
    defaultSuggestions.classList.add("hide");

    noResults.classList.remove("hide");
    return;
  }

  totalCount = data[selectedFormat].pageInfo.total;
  document.getElementById("results-total").textContent = `${totalCount} ${
    totalCount > 1 ? "stories" : "story"
  }`;

  noResults.classList.add("hide");

  data[selectedFormat].edges.forEach((edge) => {
    const node = edge.node;
    const listItem = document.createElement("div");
    listItem.classList.add("list-item");
    const postLink = document.createElement("a");
    const postImage = document.createElement("img");
    const postTitle = document.createElement("h4");
    const postMeta = document.createElement("div");
    postMeta.classList.add("post-meta");
    const byline = document.createElement("span");
    const postDate = document.createElement("span");

    listItem.appendChild(postLink);
    postLink.appendChild(postImage);
    postLink.appendChild(postTitle);
    postLink.appendChild(postMeta);
    postMeta.appendChild(byline);
    postMeta.appendChild(postDate);

    postLink.href = node.link;
    postTitle.textContent = node.title;

    if (node.featuredImage === null || node.featuredImage.node.srcSet === null) {
      postImage.src = `${domain}/gql-src/no-image.png`;
    } else {
      postImage.srcset = node.featuredImage.node.srcSet;
      postImage.sizes = node.featuredImage.node.sizes;
    }

    if (!node.byline.nodes.length) {
      byline.textContent = "";
    } else {
      byline.textContent = node.byline.nodes[0].name;
    }
    postDate.textContent = formatDate(node.date);
    document.getElementById("post-results").appendChild(listItem);
  });

  if (data[selectedFormat].pageInfo.hasNextPage) {
    cursor = data[selectedFormat].pageInfo.endCursor;
    const loadMore = document.createElement("button");
    loadMore.classList.add("load-more");
    loadMore.textContent = "Load More";
    loadMore.addEventListener("click", () => fetchArticles(false));
    resultsFooter.innerHTML = "";

    resultsFooter.appendChild(loadMore);
  } else {
    resultsFooter.innerHTML = preloader(false);
  }

  defaultSuggestions.classList.add("hide");
  results.classList.remove("hide");
}

// Search for topics
async function searchTopic() {
  document.getElementById("topics-suggestions").classList.add("hide");
  const searchValue = document.getElementById("searchTopic").value;
  const topicsResults = document.getElementById("topics-results");
  const topicsActions = document.querySelector(".tax-search-actions.topic");
  topicsActions.innerHTML = preloader(true);

  if (!searchValue.length) {
    topicsResults.innerHTML = "";
    topicsResults.classList.add("hide");
    topicsActions.innerHTML = "";
    return;
  }

  const resp = await fetch(
    `${domain}/graphql?query=query{topics(first:10,where:{nameLike:\"${searchValue}\"}){edges{node{name slug}}}}`,
  );
  const { data } = await resp.json();

  if (data.topics.edges.length === 0) {
    topicsResults.innerHTML = "<p>No topics found</p>";
    topicsResults.classList.remove("hide");
    return;
  }

  if (searchValue.length > 0 && data.topics.edges.length > 0) {
    document.getElementById("topics-suggestions").classList.add("hide");
  }

  topicsResults.classList.remove("hide");
  topicsResults.innerHTML = "";
  topicsResults.style.top = `${document.querySelector(".tax-item-wrapper.topics").offsetHeight}px`;

  data.topics.edges.forEach((edge) => {
    const node = edge.node;
    const topic = document.createElement("span");

    topic.addEventListener("click", () => {
      if (!selectedTopics.includes(node.slug)) {
        createTaxTag("topic", node.name, node.slug);

        if (searchValue.length > 0) {
          // fetchArticles();
        }
        fetchArticles(true);
        // clearSearch();
      }
    });

    topic.textContent = node.name;
    highlightResults(searchValue, topic);
    topicsResults.appendChild(topic);
  });

  if (document.querySelector(".tax-search-actions.topic .preloader-wrapper")) {
    topicsActions.removeChild(
      document.querySelector(".tax-search-actions.topic .preloader-wrapper"),
    );
  }
}

// Search for locations
async function searchLocation() {
  document.getElementById("locations-suggestions").classList.add("hide");
  const searchValue = document.getElementById("searchLocation").value;
  const locationsResults = document.getElementById("locations-results");
  const locationActions = document.querySelector(".tax-search-actions.location");
  locationActions.innerHTML = preloader(true);

  if (!searchValue.length) {
    locationsResults.innerHTML = "";
    locationsResults.classList.add("hide");
    locationActions.innerHTML = "";
    return;
  }

  const resp = await fetch(
    `${domain}/graphql?query=query{locations(first:10,where:{nameLike:\"${searchValue}\"}){edges{node{name slug}}}}`,
  );
  const { data } = await resp.json();

  if (data.locations.edges.length === 0) {
    locationsResults.innerHTML = "<p>No results found</p>";
    locationsResults.classList.remove("hide");
    return;
  }

  if (searchValue.length > 0) {
    // displayClearButton("#searchLocation", ".tax-search-actions.location");
  }

  locationsResults.classList.remove("hide");

  locationsResults.innerHTML = "";

  locationsResults.style.top = `${
    document.querySelector(".tax-item-wrapper.locations").offsetHeight
  }px`;

  data.locations.edges.forEach((edge) => {
    const node = edge.node;
    const location = document.createElement("span");

    location.addEventListener("click", () => {
      if (!selectedLocations.includes(node.slug)) {
        createTaxTag("location", node.name, node.slug);

        if (searchValue.length > 0) {
          fetchArticles(true);
        }
      }
    });

    location.textContent = node.name;
    highlightResults(searchValue, location);
    locationsResults.appendChild(location);
  });

  locationActions.removeChild(
    document.querySelector(".tax-search-actions.location .preloader-wrapper"),
  );
}

// Articles format filter
function formatFilter() {
  const searchValue = document.getElementById("searchFormat").value;

  if (!searchValue.length) {
    formatResults.classList.add("hide");
    return;
  }

  if (searchValue.length > 0) {
    document.getElementById("formats-suggestions").classList.add("hide");
  }

  const transformedSearch = searchValue.replace(/\ /g, "").toLowerCase();

  let filteredOptions = Object.values(formatOptions).filter((value) =>
    value[0].toLowerCase().includes(transformedSearch),
  );

  if (Object.keys(filteredOptions).length === 0) {
    formatResults.innerHTML = "<p>No format found</p>";
    formatResults.classList.remove("hide");
    return;
  }

  formatResults.innerHTML = "";

  Object.values(filteredOptions).forEach((format) => {
    const formatSpan = document.createElement("span");
    formatSpan.textContent = format[0];

    formatSpan.addEventListener("click", () => {
      createFormatTag(format[0], format[1]);
    });

    highlightResults(searchValue, formatSpan);
    formatResults.appendChild(formatSpan);
  });

  formatResults.style.top = `${document.querySelector(".tax-item-wrapper.format").offsetHeight}px`;
  formatResults.classList.remove("hide");
}
