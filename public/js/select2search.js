function matchStart(params, data) {
// If there are no search terms, return all of the data
if ($.trim(params.term) === '') { return data; }

// Do not display the item if there is no 'text' property
if (typeof data.text === 'undefined') { return null; }

// `params.term` is the user's search term
// `data.id` should be checked against
// `data.text` should be checked against
var q = params.term.toLowerCase();
if (data.text.toLowerCase().indexOf(q) > -1 || data.id.toLowerCase().indexOf(q) > -1) {
    return $.extend({}, data, true);
}

// Return `null` if the term should not be displayed
return null;
}
