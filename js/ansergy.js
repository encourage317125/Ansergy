/**
 * Theme functions file
 *
 * Contains handlers for navigation, accessibility, header sizing
 * footer widgets and Featured Content slider
 *
 */
( function( $ ) {
    function onLoadBookmark(bookmark_id){
        var tableKey= 'DataTables_table_1_/ansergy/';
        tableKey ='DataTables_table_1_/';
        var settings = '{"time":1469989616911,"start":0,"length":10,"order":[[0,"asc"]],"search":{"search":"","smart":false,"regex":false,"caseInsensitive":true},"columns":[{"visible":true,"search":{"search":"","smart":true,"regex":false,"caseInsensitive":true}},{"visible":true,"search":{"search":"","smart":true,"regex":false,"caseInsensitive":true}},{"visible":true,"search":{"search":"","smart":true,"regex":false,"caseInsensitive":true}},{"visible":true,"search":{"search":"","smart":true,"regex":false,"caseInsensitive":true}},{"visible":true,"search":{"search":"","smart":true,"regex":false,"caseInsensitive":true}},{"visible":true,"search":{"search":"","smart":true,"regex":false,"caseInsensitive":true}},{"visible":false,"search":{"search":"","smart":true,"regex":false,"caseInsensitive":true}},{"visible":true,"search":{"search":"","smart":true,"regex":false,"caseInsensitive":true}},{"visible":true,"search":{"search":"","smart":true,"regex":false,"caseInsensitive":true}},{"visible":false,"search":{"search":"","smart":true,"regex":false,"caseInsensitive":true}}]}';
        jQuery.post(
            vars.ajaxurl,
            {
                'action': 'read_bookmark',
                'post_id':  bookmark_id
            })
            .done(
                function(response){
                    console.log(response);
                    settings = JSON.parse(response);
                    console.log(settings.table[0]);
                    if(typeof settings !== undefined){
                        console.log('now setting');
                        localStorage.setItem(tableKey ,settings.table[0]);
                    }

                }
            )
            .fail(function(err){
                console.log(err);
            });

    }
    var body    = $( 'body' ),
        _window = $( window ),
        nav, button, menu;

    nav = $( '#primary-navigation' );
    button = nav.find( '.menu-toggle' );
    menu = nav.find( '.nav-menu' );


    _window.load( function() {
        // Arrange footer widgets vertically.
       console.log('--------------------all tables---------------------------');
       console.log($('table.wpDataTable').length);
       console.log('--------------------all tables---------------------------');
        if ( $.fn.dataTable.isDataTable( '#table_1' ) ) {
            /*table = $('#table_1').DataTable({
                retrieve: true,
                "bStateSave": true,
                "fnStateSave": function (oSettings, oData) {
                    localStorage.setItem( 'DataTables', JSON.stringify(oData) );
                },
                "fnStateLoad": function (oSettings) {
                    return JSON.parse( localStorage.getItem('DataTables') );
                }
            });
*/
            console.log($('#table_1').DataTable().state());
        }
        else {
            table = $('#table_1').DataTable( {
                paging: false
            } );
        }
    } );
    $('.testlink').click(function(){
        if ( $.fn.dataTable.isDataTable( '#table_1' ) ) {
            /*table = $('#table_1').DataTable({
             retrieve: true,
             "bStateSave": true,
             "fnStateSave": function (oSettings, oData) {
             localStorage.setItem( 'DataTables', JSON.stringify(oData) );
             },
             "fnStateLoad": function (oSettings) {
             return JSON.parse( localStorage.getItem('DataTables') );
             }
             });
             */
            console.log('tseting');
            console.log($('#table_1').DataTable().state.save());
            console.log($('#table_1').DataTable().state());
        }
        else {
            table = $('#table_1').DataTable( {
                paging: false
            } );
        }
    });

    jQuery("#bookmarks-list-widget")
        .on('loaded.jstree', function() {
            debugger
            jQuery("#bookmarks-list-widget").jstree('close_all', -1);
        })
        .jstree({
            "core": {
                "animation": 0,
                "check_callback": true
            },
            "types": {
                "types": {
                    "#": {
                        "max_children": 1,
                        "max_depth": 2,
                        "valid_children": ["root"]
                    },
                    "root": {
                        "icon": "/static/3.3.1/assets/images/tree_icon.png",
                        "valid_children": ["default"]
                    },
                    "default": {
                        "valid_children": ["default", "file"]
                    },
                    "file": {
                        "icon": "glyphicon glyphicon-file",
                        "valid_children": []
                    }
                }
            },
            "plugins": [
               "state", "types"
            ]
        })
        .on('refresh.jstree', function() {
            debugger
            jQuery("#bookmarks-list-widget").jstree('close_all', -1);
        })
        .on('activate_node.jstree', function(e ,data){
         debugger
         console.log('node clicked');
         var curNode = data.node;
         if(curNode.data.jstree.type =='folder')
            return false;
         var bookmarkId = curNode.data.bookmarkId;
         var bookmarkUrl = curNode.data.bookmarkUrl;
         onLoadBookmark(bookmarkId);
         window.location.href=bookmarkUrl;
         });
        /*.on('dblclick.jstree', function(e ,data){
            debugger
            var curNode = jQuery(e.target).closest("li")[0];
            var nodeType = JSON.parse(curNode.dataset.jstree);
            if(nodeType.type == 'folder')
                return false;
            var bookmarkId = curNode.dataset.bookmarkId;
            var bookmarkUrl = curNode.dataset.bookmarkUrl;
            onLoadBookmark(bookmarkId);
            var wc = window.open(bookmarkUrl, 'new');
        });*/

} )( jQuery );
