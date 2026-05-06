const Ziggy = {"url":"http:\/\/localhost","port":null,"defaults":{},"routes":{"boost.browser-logs":{"uri":"_boost\/browser-logs","methods":["POST"]},"teacher.lessons.name":{"uri":"teacher\/lessons","methods":["GET","HEAD"]},"teacher.lessons.store":{"uri":"teacher\/lessons","methods":["POST"]},"teacher.quiz.store":{"uri":"teacher\/lesson\/{lesson}\/quiz","methods":["POST"],"parameters":["lesson"],"bindings":{"lesson":"id"}},"storage.local":{"uri":"storage\/{path}","methods":["GET","HEAD"],"wheres":{"path":".*"},"parameters":["path"]},"storage.local.upload":{"uri":"storage\/{path}","methods":["PUT"],"wheres":{"path":".*"},"parameters":["path"]}}};
if (typeof window !== 'undefined' && typeof window.Ziggy !== 'undefined') {
  Object.assign(Ziggy.routes, window.Ziggy.routes);
}
export { Ziggy };
