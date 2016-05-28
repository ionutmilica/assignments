

/**
 * Parser class
 *
 * @param grammar
 * @returns {{parse: Function}}
 */
function grammar(grammar) {
    var symbols = [], terminals = [], rules = [];

    return {
        parse: function () {
            var productions = grammar.split("\n");

            for (var i = 0; i < productions.length; i++) {
                var data = productions[i].split("->");
                var term = productions[i].replace(data[0] + "->", '');
                var parts = term.split("|");

                for (var j = 0; j < parts.length; j++) {
                    rules.push({
                        name: data[0],
                        data: parts[j].split('')
                    });
                }

                // Add symbols
                if (symbols.indexOf(data[0]) == -1) {
                    symbols.push(data[0]);
                }
            }
            this.findTerminals();
        },

        findTerminals: function () {
            for (var i = 0; i < rules.length; i++) {
                for (var j = 0; j < rules[i].data.length; j++) {
                    var data = rules[i].data[j];
                    if (terminals.indexOf(data) == -1 && symbols.indexOf(data) == -1 && data != '3') {
                        terminals.push(data);
                    }
                }
            }
        },

        getRules: function() {
            return rules;
        },

        getTerminals: function() {
            return terminals;
        },

        getSymbols: function() {
            return symbols;
        },

        isSymbol: function(symbol) {
            return symbols.indexOf(symbol) != -1;
        }
    };
}


/**
 * Apply the algorithm
 *
 * @param grammar
 * @returns {{compute: Function, computeNull: Function}}
 */
function algorithm(grammar) {
    var table = {
        nullable: [],
        first: []
    };

    function isNullable(symbol) {
        if (grammar.isSymbol(symbol)) {
            return table.nullable[symbol];
        }
        return symbol == '3';
    }

    /**
     * Verifica daca pe linie: ex S->3 gasim doar sigma (3)
     *
     * @param data
     * @returns {boolean}
     */
    function canInstantiate(data)
    {
        return !(data.length == 0 || data.length == 1 && data[0] == '3');
    }

    /**
     * Verifica daca pe o linie, ex: S->3|A|C de la pozitia last la start toate elementele sunt nullable
     *
     * @param start
     * @param last
     * @param data
     * @returns {boolean}
     */
    function areNullable(start, last, data)
    {
        if ( ! canInstantiate(data)) {
            return false;
        }

        if (last < 0 || data.length <= last) {
            return true;
        }

        var _isNullable = true;
        for (var i = last; i <= start; i++) {
            if ( ! isNullable(data[i])) {
                _isNullable = false;
                break;
            }
        }

        return _isNullable;
    }

    return {
        compute: function () {
            var symbols = grammar.getSymbols(), terminals = grammar.getTerminals();
            var i;

            for (i = 0; i < symbols.length; i++) {
                table.nullable[symbols[i]] = false;
                table.first[symbols[i]] = [];
            }
            for (i = 0; i < terminals.length; i++) {
                table.first[terminals[i]] = [terminals[i]];
            }

            this.computeNull();
            this.computeFirst();
        },

        computeNull: function() {
            var wasModified, rules = grammar.getRules();

            for (i = 0; i < rules.length; i++) {
                X = rules[i].name;
                if ( ! canInstantiate(rules[i].data)) {
                   table.nullable[X] = true;
                }
            }
            do {
                wasModified = false;

                // Trece prin reguli
                for (var i = 0; i < rules.length; i++) {
                    var data = rules[i].data;
                    var X = rules[i].name;

                    var nullable = true;
                    // Trece prin data
                    for (var j = 0; j < data.length; j++) {
                        var symbol = data[j];
                        if ( ! isNullable(symbol)) {
                            nullable = false;
                            break;
                        }
                    }
                    if (nullable == true && table.nullable[X] == false) {
                        table.nullable[X] = true;
                        wasModified = true;
                    }
                }
            } while (wasModified);
        },

        computeFirst: function () {
            var wasModified, rules = grammar.getRules();

            do {
                wasModified = false;
                for (var i = 0; i < rules.length; i++) {
                    var data = rules[i].data;
                    var X = rules[i].name;
                    var n = data.length;

                    if ( ! canInstantiate(data)) {
                        continue;
                    }

                    _new = _.union(table.first[X], table.first[data[0]]);

                    if (_.difference(_new, table.first[X]).length) {
                        table.first[X] = _new;
                        wasModified = true;
                    }
                    for (var tmp = 1; tmp < n; tmp++) {
                        if (areNullable(0, tmp - 1, data)) {
                            _new = _.union(table.first[X], table.first[data[tmp]]);
                            if (_.difference(_new, table.first[X]).length) {
                                table.first[X] = _new;
                                wasModified = true;
                            }
                        }
                    }
                }

            } while (wasModified);
        },

        getTable: function () {
            return table;
        }
    };
}

