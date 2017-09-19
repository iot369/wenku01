
// Provide a default path to dwr.engine
if (dwr == null) var dwr = {};
if (dwr.engine == null) dwr.engine = {};
if (DWREngine == null) var DWREngine = dwr.engine;

if (eduDwr == null) var eduDwr = {};
eduDwr._path = '/dwr';
eduDwr.getAuctionById = function(p0, callback) {
  dwr.engine._execute(eduDwr._path, 'eduDwr', 'getAuctionById', p0, callback);
}
eduDwr.getSaleTranById = function(p0, callback) {
  dwr.engine._execute(eduDwr._path, 'eduDwr', 'getSaleTranById', p0, callback);
}
eduDwr.addcommonclass = function(p0, callback) {
  dwr.engine._execute(eduDwr._path, 'eduDwr', 'addcommonclass', p0, callback);
}
eduDwr.delcommonclass = function(p0, callback) {
  dwr.engine._execute(eduDwr._path, 'eduDwr', 'delcommonclass', p0, callback);
}
