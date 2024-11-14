var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
  return new bootstrap.Tooltip(tooltipTriggerEl)
});

function copyToClipboard() {
  // Find the <pre> tag content
  const codeContent = document.getElementById("code").innerText;

  // Create a temporary textarea element to hold the content
  const tempTextArea = document.createElement("textarea");
  tempTextArea.value = codeContent;

  // Append the textarea to the document, copy its content, and remove it
  document.body.appendChild(tempTextArea);
  tempTextArea.select();
  document.execCommand("copy");
  document.body.removeChild(tempTextArea);

  // Change the button text to "Copied!" temporarily
  const copyButton = document.getElementById("copyButton");
  const originalText = copyButton.innerText;
  copyButton.innerText = "Copied!";

  // Disable the button to prevent multiple clicks
  copyButton.disabled = true;

  // Revert the button text back after 1 second
  setTimeout(() => {
    copyButton.innerText = originalText;
    copyButton.disabled = false; // Re-enable the button
  }, 1000);
}

// Usage:
// toggleSidebar(true)  // to collapse
// toggleSidebar(false) // to expand
function toggleSidebar(collapse) {
  // Get elements
  const elements = {
    sidebar: document.getElementById('sidebarMenu'),
    svgicon: document.getElementById('svg-panel-close'),
    navbar: document.getElementById('navbar'),
    main: document.getElementById('main')
  };

  // Check if all elements exist
  if (!Object.values(elements).every(Boolean)) {
    throw new Error('Required elements not found');
  }

  const { sidebar, svgicon, navbar, main } = elements;

  // Toggle classes based on collapse state
  sidebar.classList.toggle('col-md-3', !collapse);
  sidebar.classList.toggle('col-md-1', collapse);
  sidebar.classList.toggle('col-lg-2', !collapse);
  sidebar.classList.toggle('col-lg-1', collapse);
  sidebar.classList.toggle('collapsed', collapse);

  navbar.classList.toggle('nonvisible', collapse);
  svgicon.classList.toggle('rotate-180', collapse);

  main.classList.toggle('col-md-9', !collapse);
  main.classList.toggle('col-md-12', collapse);
  main.classList.toggle('col-lg-10', !collapse);
  main.classList.toggle('col-lg-12', collapse);
  main.classList.toggle('expanded', collapse);
}


document.addEventListener('DOMContentLoaded', function () {
  // Toggle Example of use
  const toggleText = document.getElementById('toggleExampleOfUse') || null;
  const toggleIcon = document.getElementById('toggleIconExampleOfUse') || null;
  const collapseElement = document.getElementById('collapseExampleOfUse') || null;

  if (collapseElement !== null) {
    collapseElement.addEventListener('shown.bs.collapse', function () {
      toggleIcon.classList.remove('fa-square-plus');
      toggleIcon.classList.add('fa-square-minus');
      toggleText.title = 'Click to collapse';
    });

    collapseElement.addEventListener('hidden.bs.collapse', function () {
      toggleIcon.classList.remove('fa-square-minus');
      toggleIcon.classList.add('fa-square-plus');
      toggleText.title = 'Click to expand';
    });
  }

  // Toggle Dataset
  const toggleTextDataset = document.getElementById('toggleDataset') || null;
  const toggleIconDataset = document.getElementById('toggleIconDataset') || null;
  const collapseElementDataset = document.getElementById('collapseDataset') || null;

  if (collapseElementDataset !== null) {
    collapseElementDataset.addEventListener('shown.bs.collapse', function () {
      toggleIconDataset.classList.remove('fa-square-plus');
      toggleIconDataset.classList.add('fa-square-minus');
      toggleTextDataset.title = 'Click to collapse';
    });

    collapseElementDataset.addEventListener('hidden.bs.collapse', function () {
      toggleIconDataset.classList.remove('fa-square-minus');
      toggleIconDataset.classList.add('fa-square-plus');
      toggleText.title = 'Click to expand';
    });
  }

  // Restore side bar state
  const sideBarState = localStorage.getItem("sidebar");
  if (sideBarState === "collapsed") {
    toggleSidebar(true)
  } else {
    toggleSidebar(false)
  }
});

// JavaScript to toggle sidebar visibility
document.getElementById('btn-panel-close').addEventListener('click', function() {
  if (this.title === "Collapse") {
    this.title = "Expand";
    localStorage.setItem("sidebar", "collapsed");
    toggleSidebar(true)
  } else {
    this.title = "Collapse";
    localStorage.setItem("sidebar", "expanded");
    toggleSidebar(false)
  }
});
