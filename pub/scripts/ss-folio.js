/**
 * Portfolio tabs
 */
function setActiveTab(tab) {
  var tabs = document.getElementsByClassName('ss-folio-tab'),
      apparel = document.getElementById('ss-folio-apparel'),
      apparelTab = document.getElementsByClassName('ss-folio-tab--apparel')[0],
      signs = document.getElementById('ss-folio-signs'),
      signsTab = document.getElementsByClassName('ss-folio-tab--signs')[0],
      vinyl = document.getElementById('ss-folio-vinyl'),
      vinylTab = document.getElementsByClassName('ss-folio-tab--vinyl')[0],
      other = document.getElementById('ss-folio-other'),
      otherTab = document.getElementsByClassName('ss-folio-tab--other')[0];

  /**
   * remove active class from tabs
   */
  for (i = 0; i < tabs.length; i++) {
    var thisTab = tabs[i];
    thisTab.classList.remove('active');
  };

  /**
   * Hide the galleries
   */
  apparel.style.display = 'none';
  signs.style.display = 'none';
  vinyl.style.display = 'none';
  other.style.display = 'none';

  /**
   * Set the new active gallery
   */
  if (tab == 'ss-folio-apparel') {
    apparelTab.classList.add('active');
    apparel.style.display = 'block';
  }
  else if (tab == 'ss-folio-signs') {
    signsTab.classList.add('active');;
    signs.style.display = 'block';
  }
  else if (tab == 'ss-folio-vinyl') {
    vinylTab.classList.add('active');;
    vinyl.style.display = 'block';
  }
  else if (tab == 'ss-folio-other') {
    otherTab.classList.add('active');;
    other.style.display = 'block';
  }
};

/**
 * Open portfolio image modal
 */
function openModal(e) {
  var modal = document.getElementsByClassName('ss-folio-modal')[0],
      image = document.getElementsByClassName('ss-folio-modal-image')[0];

  modal.style.display = 'block';
  image.src = e.target.src;
};

/**
 * Close portfolio image modal
 */
function closeModal() {
  var modal = document.getElementsByClassName('ss-folio-modal')[0],
      image = document.getElementsByClassName('ss-folio-modal-image')[0];

  modal.style.display = 'none';
  image.src = '';
};
