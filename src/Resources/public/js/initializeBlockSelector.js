/*
 * This file is part of the ekino/sonata project.
 *
 * (c) Ekino
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

const handleBlockSelectLinkClick = e => {
  const link = e.target.closest('.BlockSelectModal_SelectLink');
  const blockCode = link.dataset.value;

  const composerContainer = link.closest('.page-composer__container__view');
  const sonataSelect = composerContainer.querySelector('select.page-composer__block-type-selector__select');
  const sonataAddButton = composerContainer.querySelector('.page-composer__block-type-selector__confirm');

  // Select the target option containing this value
  const option = $('.page-composer__block-type-selector__select option[value="' + blockCode + '"]');

  if (!option) {
    e.stopPropagation();
    return;
  }

  sonataSelect.value = blockCode;
  sonataSelect.dispatchEvent(new Event('change'));

  // Trigger block add click
  sonataAddButton.click();
};

const handleCategoryLinkClick = e => {
  e.preventDefault();

  // Remove active state from other category links
  const categoryLinks = e.target.closest('.BlockSelectModal').querySelectorAll('.BlockSelectModal_Categories_Link-active');
  categoryLinks.forEach(categoryLink => {
    categoryLink.classList.remove('BlockSelectModal_Categories_Link-active');
  });

  const link = e.target.closest('.BlockSelectModal_Categories_Link');
  const category = link.dataset.category;

  // Set active state on link
  link.classList.add('BlockSelectModal_Categories_Link-active');

  // Hide all block panels
  const modal = link.closest('.BlockSelectModal');
  const blockLists = modal.querySelectorAll('.BlockSelectModal_List');
  blockLists.forEach(blockList => {
    blockList.classList.add('BlockSelectModal_List-hidden');
  });

  // Display the category block panel
  const displayedBlockList = modal.querySelector(`[data-block-category="${category}"]`);
  if (displayedBlockList) {
    displayedBlockList.classList.remove('BlockSelectModal_List-hidden');
  }
};

/**
 * Initialize block selector behavior on all suitable children of the given node
 *
 * @param {Element} node
 */
export default function initializeBlockSelector(node) {
  // If Page compose not loaded, leaving.
  if (!window.PageComposer || !node.querySelectorAll) {
    return;
  }

  const modal = node.querySelector('.BlockSelectModal');
  if (!modal) {
    return;
  }

  const composerContainer = modal.closest('.page-composer__container__view');
  const sonataSelect = composerContainer.querySelector('.page-composer__block-type-selector__select');
  const sonataAddButton = composerContainer.querySelector('.page-composer__block-type-selector__confirm');
  sonataSelect.style.display = 'none';
  sonataAddButton.style.display = 'none';

  const selectLinks = modal.querySelectorAll('.BlockSelectModal_SelectLink');
  selectLinks.forEach(link => {
    link.addEventListener('click', handleBlockSelectLinkClick);
  });

  const categoryLinks = modal.querySelectorAll('.BlockSelectModal_Categories_Link');
  categoryLinks.forEach(link => {
    link.addEventListener('click', handleCategoryLinkClick);
  });
}
