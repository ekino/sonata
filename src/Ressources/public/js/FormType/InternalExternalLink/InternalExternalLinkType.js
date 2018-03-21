/*
 * This file is part of the Sonata for Ekino project.
 *
 * (c) 2018 - Ekino
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

import Logger from '../../Utils/Logger';

class InternalExternalLinkType {
    /**
     * @param {HTMLElement} element
     */
    constructor (element) {
        this.element = element;
    }

    initialize () {
        this.pageField = this.element.querySelector('select.LinkWidget_PageNameField');
        this.pageFieldId = this.pageField.getAttribute('id');
        this.pageField.onchange = this.handlePageChange.bind(this);

        this.pageParamsField = this.element.querySelector('.LinkWidget_PageParamsField');
        this.pageParamsFieldId = this.pageParamsField.getAttribute('id');

        this.linkTypeField = this.element.querySelector('select.LinkWidget_LinkTypeField');
        this.linkTypeField.onchange = this.handleLinkTypeChange.bind(this);

        this.pagePathBlock = this.element.querySelector('.LinkWidget_PagePath');

        this.toggleLink = this.element.querySelector('.LinkWidget_ToggleLink');
        if (this.toggleLink) {
            this.toggleLink.onclick = this.handleToggleLinkClick.bind(this);
        }

        this.createParameterContainer();
        this.updateDisplay();
    }

    /**
     * Hide preview and display link fields
     */
    open () {
        if (this.toggleLink) {
            this.toggleLink.click();
        }
    }

    /**
     * Create the page parameter container DOM node
     */
    createParameterContainer () {
        const pageFieldContainer = this.element.querySelector(`#sonata-ba-field-container-${this.pageFieldId}`);
        const sonataParameterContainerId = `sonata-ba-field-container-${this.pageParamsFieldId}`;

        // Do not create container if it already exists
        const pageParameterContainer = this.element.querySelector(`#${sonataParameterContainerId}`);
        if (pageParameterContainer) {
            this.pageParametersContainer = pageParameterContainer;
            return;
        }

        const template = `
            <div class="form-group" id="${sonataParameterContainerId}" style="display: block;">
                <label class="control-label required LinkWidget_ParameterLabel">Paramètres</label>
                <div class="LinkWidget_ParameterContainer"></div>
            </div>
        `;
        pageFieldContainer.insertAdjacentHTML('afterend', template);

        this.pageParametersContainer = this.element.querySelector(`#${sonataParameterContainerId}`);
    }

    /**
     * Update form type display according to its selected values
     */
    updateDisplay () {
        if (this.linkTypeField.value === 'page') {
            // In page selection mode, we must display the page parameter section
            this.setParameterContainerVisible(true);
            this.updateParameterTable();

            // Update the displayed page path if any
            if (this.pagePathBlock) {
                let pagePath = this.getSelectedPagePath();

                if (!pagePath) {
                    this.pagePathBlock.innerHTML = '-';
                    return;
                }

                const parameters = this.getPageParameters();

                for (let parameter in parameters) {
                    pagePath = pagePath.replace(`{${parameter}}`, parameters[parameter]);
                }

                this.pagePathBlock.innerHTML = pagePath;
            }

        } else {
            this.setParameterContainerVisible(false);
        }

        // If error, display InternalExternalLink element
        if (this.element.querySelector('.sonata-ba-field-error')) {
            const formContainer = this.element.querySelector('.LinkWidget_Form');
            formContainer.classList.remove('LinkWidget_Form-hidden');

            const previewContainer = this.element.querySelector('.LinkWidget_Preview');
            if (previewContainer) {
                previewContainer.remove();
            }
        }
    }

    /**
     * Update the parameter section according to selected page
     */
    updateParameterTable () {
        const routePattern = this.getSelectedPagePath();
        const parameters = routePattern ? routePattern.match(/{(\w+)}/g) || [] : [];
        const parameterLabel = this.element.querySelector('.LinkWidget_ParameterLabel');

        // Selected route has no parameters, leaving
        if (parameters.length === 0) {
            parameterLabel.style.display = 'none';
            this.clearParameters();
            return;
        }
        parameterLabel.style.display = 'inherit';

        // Removing all existing fields in table
        const parameterContainer = this.getParameterContainer();
        while (parameterContainer.firstChild) {
            parameterContainer.removeChild(parameterContainer.firstChild);
        }

        this.setParameterContainerVisible(true);

        parameters.forEach((placeholder) => {
            this.createParameterRow(placeholder);
        });
    }

    /**
     * Create an input row for the given parameter in the parameter table
     *
     * @param {string} parameter
     */
    createParameterRow (parameter) {
        // Remove "{" and "}" chars from around the parameter
        const parameterName    = parameter.substring(1, parameter.length - 1);
        const parameterValue   = this.getPageParameter(parameterName);
        const parameterFieldId = `${this.pageParamsFieldId}_${parameterName}`;

        const template = `
            <div class="input-group">
                <span class="input-group-addon"><code>${parameterName}</code></span>
                <input type="text"
                       id="${parameterFieldId}"
                       data-param-name="${parameterName}"
                       required="required"
                       class="form-control LinkWidget_PageParameter"
                       placeholder="Saisissez une valeur..."
                       value="${parameterValue}">
            </div>
        `;

        this.getParameterContainer().insertAdjacentHTML('afterbegin', template);

        // Bind change event to the new field
        const parameterField = this.pageParametersContainer.querySelector(`#${parameterFieldId}`);
        parameterField.onchange = this.handleParameterChange.bind(this);
    }

    /**
     * Handle user input in a page parameter field
     */
    handleParameterChange () {
        this.updatePageParameters();
    }

    /**
     * Handle link page selection change
     *
     * @param {Event} event
     */
    handlePageChange (event) {
        this.clearParameters();
        this.updateParameterTable();
    }

    /**
     * Handle link type selection change
     *
     * @param {Event} event
     */
    handleLinkTypeChange (event) {
        this.updateDisplay();
    }

    /**
     * Handle click on toggle link
     */
    handleToggleLinkClick () {
        const formContainer = this.element.querySelector('.LinkWidget_Form');
        formContainer.classList.remove('LinkWidget_Form-hidden');

        const previewContainer = this.element.querySelector('.LinkWidget_Preview');
        previewContainer.remove();
    }

    /**
     * Clear page parameters and hide parameter container
     */
    clearParameters () {
        this.setParameterContainerVisible(false);
        this.pageParamsField.value = "{}";
    }

    /**
     * Set parameter container visibility
     *
     * @param {boolean} isVisible
     */
    setParameterContainerVisible (isVisible) {
        this.pageParametersContainer.style.display = isVisible ? 'block' : 'none';
    }

    /**
     * Return the selected page route pattern, containing its route
     *
     * @return {string|null}
     */
    getSelectedPagePath () {
        // The label is like "Page name (/pattern/{param})", we want to extract "(/pattern/{param})"
        let routePattern = this.pageField.selectedOptions[0].innerText.match(/\(.+\)/g);

        if (!routePattern || routePattern.length === 0) {
            return null;
        }

        // Remove parenthesis around pattern
        return routePattern[0].substring(1, routePattern[0].length - 1);
    }

    /**
     * Return current page parameters
     *
     * @return {object}
     */
    getPageParameters () {
        const value = this.pageParamsField.value;

        if (value.length > 0) {
            try {
                return JSON.parse(value);
            } catch (error) {
                Logger.error(`Failed to JSON parse parameters: ${value}`);
            }
        }

        return {};
    }

    /**
     * Return the given page parameter
     *
     * @param {string} name
     * @return {string}
     */
    getPageParameter (name) {
        const parameters = this.getPageParameters();

        return parameters[name] ? parameters[name] : '';
    }

    /**
     * Update page parameters with user inputs
     */
    updatePageParameters () {
        const parameterInputs = this.element.querySelectorAll('.LinkWidget_PageParameter');

        const parameters = {};
        parameterInputs.forEach((parameterInput) => {
            const value = parameterInput.value;
            const name  = parameterInput.dataset.paramName;

            parameters[name] = value;
        });

        this.pageParamsField.value = JSON.stringify(parameters);
    }

    /**
     * Return the parameter container node
     *
     * @return {Element|null}
     */
    getParameterContainer () {
        return this.pageParametersContainer.querySelector('.LinkWidget_ParameterContainer');
    }
}

export default InternalExternalLinkType;
