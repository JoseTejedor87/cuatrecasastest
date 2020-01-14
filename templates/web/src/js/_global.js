// Global
web.global = {
    init: function(){ // Load all global functions here
        console.log("load global functions");
        web.global.loadHeader();
    },
    loadHeader: function(){ // Some specific function
        console.log("loadHeader()");
    }
}

// Run the global stuff
web.global.init();