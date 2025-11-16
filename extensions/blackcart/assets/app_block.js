/**
 * PRIVATE listener for the variant picker when the toggle is being used.
 */
function variantPickerToggleListener(selectedVariantId, isTbybDefault) {
    //reset option selector
    document.getElementById('tbybPurchaseOption').disabled = true;
    document.getElementById('payTodayPurchaseOption').checked = true;
    if (isTbybActionEnabled(selectedVariantId)) {
        document.getElementById('tbybPurchaseOption').disabled = false;
        if (isTbybDefault) {
            document.getElementById('tbybPurchaseOption').checked = true;
        }
    }
    tbybOptionSelected = document.querySelector('span[id="blackcartTbyb"] form input:checked').value;
}

/**
 * PRIVATE listener for the variant picker when the button is being used.
 */
function variantPickerButtonListener(selectedVariantId, isTbybDefault) {
    document.getElementById('tbybButton').disabled = true;
    if (isTbybActionEnabled(selectedVariantId)) {
        document.getElementById('tbybButton').disabled = false;
    }
}

function toggleStateChecker() {
    var tbybToggleOptionTextDisabledWhenMaxItems = document.getElementById('tbybToggleOptionTextDisabledWhenMaxItems');
    var tbybToggleOptionTextAlreadyLimitReached = document.getElementById('tbybToggleOptionTextAlreadyLimitReached');
    var tbybToggleOptionTextDisabled = document.getElementById('tbybToggleOptionTextDisabled');
    var tbybSubText = document.getElementById('tbybSubText');
    var tbybPurchaseOption = document.getElementById('tbybPurchaseOption');

    tbybToggleOptionTextDisabledWhenMaxItems.style.display = 'none';
    tbybToggleOptionTextAlreadyLimitReached.style.display = 'none';
    tbybToggleOptionTextDisabled.style.display = 'none';
    tbybSubText.style.display = 'block';

    if (tbybPurchaseOption.hasAttribute('disabled')) {
        tbybSubText.style.display = 'none';
        if (disabledVariants.hasOwnProperty(selectedVariantId)) {
            tbybToggleOptionTextDisabledWhenMaxItems.style.display = 'none';
            tbybToggleOptionTextAlreadyLimitReached.style.display = 'block';
            tbybToggleOptionTextDisabled.style.display = 'none';
        } else {
            if (maxTbybItems !== 'unlimited' && tbybItemsInCart >= Number(maxTbybItems)) {
                tbybToggleOptionTextDisabledWhenMaxItems.style.display = 'block';
                tbybToggleOptionTextAlreadyLimitReached.style.display = 'none';
                tbybToggleOptionTextDisabled.style.display = 'none';
            } else {
                tbybToggleOptionTextDisabledWhenMaxItems.style.display = 'none';
                tbybToggleOptionTextAlreadyLimitReached.style.display = 'none';
                tbybToggleOptionTextDisabled.style.display = 'block';
            }
        }
    }
}

function buttonStateChecker() {
    document.getElementById('tbybButtonOptionTextDisabled').style.display = 'none';
    document.getElementById('tbybOptionText').style.display = 'block';
    if (document.getElementById('tbybButton').hasAttribute('disabled')) {
        document.getElementById('tbybButtonOptionTextDisabled').style.display = 'block';
        document.getElementById('tbybOptionText').style.display = 'none';
    }
}

function updateUpfrontVariantAmount(selectedVariantId) {
    if (product?.variants?.length > 0) {
        for (let i = 0; i < product.variants.length; i++) {
            if (product.variants[i].id == selectedVariantId) {
                document.getElementById('payTodayPurchaseOptionText').innerHTML = convertCurrency(product.variants[i].price / 100, currencyDisplayFormat);
                break;
            }
        }
    }
}

//Updates text on the toggle or button depending on the variant being tbyb eligible.
function uiStateChecker(selectedVariantId) {
    if (useTbybToggle) {
        toggleStateChecker();
        updateUpfrontVariantAmount(selectedVariantId);
    } else {
        buttonStateChecker();
    }
}

function variantPickerListener(selectedVariantId, isTbybDefault) {
    if (useTbybToggle) {
        variantPickerToggleListener(selectedVariantId, isTbybDefault);
    } else {
        variantPickerButtonListener(selectedVariantId, isTbybDefault);
    }
    uiStateChecker(selectedVariantId);
}

/**
 * return whether the tbyb button or toggle should be enabled for the given variant
 */
function isTbybActionEnabled(selectedVariantId) {
    return (
        !disabledVariants.hasOwnProperty(selectedVariantId) &&
        variantsEligibleForTbyb.includes(selectedVariantId) &&
        (tbybItemsInCart < Number(maxTbybItems) || maxTbybItems == 'unlimited') &&
        isBlockVisible
    );
}

function tbybButtonListener(event) {
    tbybOptionSelected = 'tbyb';
    document.querySelectorAll('form[action^="/cart/add"] [type=submit]').forEach((formButton) => {
        formButton.click();
    });
    tbybOptionSelected = 'upfront';
}

/**
 * called when we listen to a fetch /cart/change call with a 0 quantity line item. check if the item
 * is tbyb and if so, update our ui and vars accordingly.
 * TODO: add support for multiple quantity removal rather than assuming one for tbyb items in cart
 */
async function checkAndRemoveVariant(cartLineIndex, productId) {
    let response = await fetch(window.Shopify.routes.root + 'cart.js');
    let data = await response.json();

    let item;
    if (cartLineIndex !== null && cartLineIndex !== undefined) {
        // If cartLineIndex is provided, use it to retrieve the item
        item = data.items[cartLineIndex - 1];
    } else if (productId !== null) {
        // If productId is provided, use it to find the item
        item = data.items.find(item => item.id === parseInt(productId));
    } else {
        console.error("Both cartLineIndex and productId cannot be null. Provide at least one.");
        return;
    }

    if (item.hasOwnProperty('selling_plan_allocation')) {
        if (item.selling_plan_allocation.selling_plan.id == tbybSellingPlanId) {
            delete disabledVariants[item.variant_id];
            tbybItemsInCart -= 1;
            variantPickerListener(selectedVariantId, isTbybDefault);
        }
    }
}

/**
 * update tbyb ui according to current cart state. App block state does not persist between pages
 * so the cart must be queried via ajax whenever the customer moves around the website.
 */
async function updateUiWithCurrentCartState() {
    let response = await fetch(window.Shopify.routes.root + 'cart.js');
    let data = await response.json();
    data.items.forEach((item) => {
        if (item.hasOwnProperty('selling_plan_allocation')) {
            if (item.selling_plan_allocation.selling_plan.id == tbybSellingPlanId) {
                disabledVariants[item.variant_id] = true;
                tbybItemsInCart += 1;
            }
        }
    });
    variantPickerListener(selectedVariantId, isTbybDefault);
}

/**
 * Used to convert fixed deposit amount to displayable string for presentment currency. Created because
 * we have to get the converted selling plan checkout charge amount in js which means we can't use the liquid money filter.
 */
function convertCurrency(amount, currencyDisplayFormat) {
    let amountNoDecimalsCommaSeparatorFormat = false;
    let amountNoDecimalsFormat = false;
    let amountCommaSeparatorFormat = false;
    let amountApostropheSeparatorFormat = false;
    let amountFormat = false;

    if (currencyDisplayFormat.includes('amount_no_decimals_with_comma_separator')) {
        amountNoDecimalsCommaSeparatorFormat = true;
    } else if (currencyDisplayFormat.includes('amount_no_decimals')) {
        amountNoDecimalsFormat = true;
    } else if (currencyDisplayFormat.includes('amount_with_comma_separator')) {
        amountCommaSeparatorFormat = true;
    } else if (currencyDisplayFormat.includes('amount_with_apostrophe_separator')) {
        amountApostropheSeparatorFormat = true;
    } else {
        amountFormat = true;
    }

    if (amountNoDecimalsFormat || amountNoDecimalsCommaSeparatorFormat) {
        amount = String(Math.round(amount));
    } else {
        amount = String(amount);
    }

    let convertedAmount = '';
    let hasCents = false;
    for (let i = amount.length - 1; i >= 0; i--) {
        //for cents
        if (amount[i] == '.' && i == amount.length - 3) {
            hasCents = true;
            if (amountCommaSeparatorFormat) {
                convertedAmount += ',';
            } else {
                convertedAmount += '.';
            }
            continue;
        }

        convertedAmount += amount[i];

        //add thousandth currency separator

        if ((amount.length - i) % 3 == 0 && i != 0) {
            if (amountFormat || amountNoDecimalsFormat) {
                convertedAmount += ',';
            } else if (amountCommaSeparatorFormat || amountNoDecimalsCommaSeparatorFormat) {
                convertedAmount += '.';
            } else {
                convertedAmount += "'";
            }
        }
    }

    convertedAmount = convertedAmount.split('').reverse().join('');
    if (!hasCents && !amountNoDecimalsCommaSeparatorFormat && !amountNoDecimalsFormat) {
        if (amountCommaSeparatorFormat) {
            convertedAmount += ',';
        } else {
            convertedAmount += '.';
        }
        convertedAmount += '00';
    }

    if (amountApostropheSeparatorFormat) {
        return currencyDisplayFormat.replace('{{amount_with_apostrophe_separator}}', convertedAmount);
    } else if (amountNoDecimalsFormat) {
        return currencyDisplayFormat.replace('{{amount_no_decimals}}', convertedAmount);
    } else if (amountCommaSeparatorFormat) {
        return currencyDisplayFormat.replace('{{amount_with_comma_separator}}', convertedAmount);
    } else if (amountNoDecimalsCommaSeparatorFormat) {
        return currencyDisplayFormat.replace('{{amount_no_decimals_with_comma_separator}}', convertedAmount);
    }

    // Catch all to handle spaces i.e {{ amount }} or {{amount}}

    var regex = /{{\s*amount\s*}}/i;

    return currencyDisplayFormat.replace(regex, convertedAmount);
}

function getCookie(cookiename)
{
    // Get name followed by anything except a semicolon
    var cookiestring=RegExp(cookiename+"=[^;]+").exec(document.cookie);
    // Return everything after the equal sign, or an empty string if the cookie name not found
    return decodeURIComponent(!!cookiestring ? cookiestring.toString().replace(/^[^=]+./,"") : "");
}

function parseQueryString(formDataString, charset) {
    // Split key-value pairs
    const pairs = formDataString.split('&');
    const data = {};

    pairs.forEach(pair => {
        // Split key and value
        const [key, value] = pair.split('=');
        // Decode URL encoding with the specified charset
        const decodedKey = decodeURIComponent(key.replace(/\+/g, ' '), charset);
        const decodedValue = decodeURIComponent(value.replace(/\+/g, ' '), charset);
        // Store in data structure
        data[decodedKey] = decodedValue;
    });

    return data;
}

function parseFormDataFromQueryString(data, contentTypeHeader) {
    const [mediaType, parameters] = contentTypeHeader.split(';');
    let charset = 'utf-8';
    if (parameters) {
        const charsetParam = parameters.split('=')[1];
        charset = charsetParam.trim();
    }

    const formData = parseQueryString(data, charset);
    const quantity = Object.hasOwnProperty(formData['quantity']) ? formData['quantity'] : 1;
    return [formData, quantity]
}

function addSellingPlanToBody(data, contentTypeHeader = '') {

    if (contentTypeHeader.includes("application/x-www-form-urlencoded")) {
        [formData, quantity] = parseFormDataFromQueryString(data, contentTypeHeader);
        if (formData.hasOwnProperty('quantity')) {
            formData['quantity'] = '1';
        }
        formData['selling_plan'] = tbybSellingPlanId;
        formData['selling_plan_id'] = tbybSellingPlanId;
        serialize = function(obj) {
            var str = [];
            for (var p in obj)
              if (obj.hasOwnProperty(p)) {
                str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
              }
            return str.join("&");
          }
        formData = serialize(formData);
        return [encodeURI(formData), quantity];
    } else if (typeof data === 'object') {
        const quantity = Object.fromEntries(data).quantity;
        if (quantity) {
            data.set('quantity', '1');
        }
        data.append('selling_plan', tbybSellingPlanId);
        return [data, quantity];
    } else if (contentTypeHeader.includes("application/json") && typeof data === 'string') {
        let jsonData = JSON.parse(data);
        let quantity;

        if (jsonData.items && jsonData.items.length > 0) {
            quantity = jsonData.items[0].quantity;

            jsonData.items[0].quantity = '1';
            jsonData.items[0].selling_plan = tbybSellingPlanId;
            jsonData.items[0].selling_plan_id = tbybSellingPlanId;
        } else {
            quantity = jsonData.quantity;

            jsonData.quantity = '1';
            jsonData.selling_plan = tbybSellingPlanId;
            jsonData.selling_plan_id = tbybSellingPlanId;
        }

        return [JSON.stringify(jsonData), quantity];
    }
    return [data, 1, null];
}

(function (ns, fetch) {
    if (typeof fetch !== 'function') return;

    ns.fetch = function () {
        let relativeUrl = arguments[0];
        let argumentsCopy = Object.assign({}, arguments);

        try {
            let contentTypeHeader = '';
            if (arguments.length >= 2 && arguments[1].hasOwnProperty('headers')) {
                if ('Content-Type' in arguments[1]['headers']) {
                    contentTypeHeader = arguments[1]['headers']['Content-Type'];
                }
                else if ('content-type' in arguments[1]['headers']) {
                    contentTypeHeader = arguments[1]['headers']['content-type'];
                }
            }
            if (relativeUrl.includes('/cart/change')) {
                if (contentTypeHeader.includes('application/x-www-form-urlencoded')) {
                    let data = arguments[1]['body'];
                    let id = data.split('%3A')[0].split('=')[1];
                    let quantity = data.split('quantity=')[1];
                    if (quantity == 0) {
                        checkAndRemoveVariant(null, id);
                    }
                } else {
                    let data = JSON.parse(arguments[1]['body']);
                    let quantity = data.quantity;
                    if (quantity == 0) {
                        if (data.hasOwnProperty('line')) {
                            checkAndRemoveVariant(data.line, null);
                        } else if (data.hasOwnProperty('id')) {
                            let id = data.id.split(":")[0];
                            checkAndRemoveVariant(null, id);
                        }
                    }
                }

                return fetch.apply(this, arguments);
            }

            if (!relativeUrl.includes('/cart/add')) {
                return fetch.apply(this, arguments);
            }

            if (tbybOptionSelected === 'upfront' || !isTbybActionEnabled(selectedVariantId)) {
                return fetch.apply(this, arguments);
            }

            let formData = arguments[1]['body'];
            //form data
            [updatedDataWithSellingPlan, quantity] = addSellingPlanToBody(formData, contentTypeHeader);

            // disable ui for the variant added as tbyb
            disabledVariants[selectedVariantId] = true;
            //we only assume one can be added via ui but its written to add quantity to support a future change to allow any quantity amount
            tbybItemsInCart += Number(quantity);
            variantPickerListener(selectedVariantId, isTbybDefault);
            arguments[1]['body'] = updatedDataWithSellingPlan;
        } catch (error) {
            console.log(error);
        } finally {
            return fetch.apply(this, arguments);
        }
    };
})(window, window.fetch);

(function() {
    const originalSend = XMLHttpRequest.prototype.send;

    XMLHttpRequest.prototype.send = function(body) {
        if (this._url.includes('/cart/add.js')) {
            if (typeof body === 'string') {
                if (tbybOptionSelected === 'tbyb' && isTbybActionEnabled(selectedVariantId)) {
                    [updatedDataWithSellingPlan, quantity] = addSellingPlanToBody(body, 'application/x-www-form-urlencoded')
                    body = updatedDataWithSellingPlan
                }
            }
        }
        return originalSend.apply(this, [body]);
    };
})();
