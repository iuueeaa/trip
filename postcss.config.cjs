const discardComments = {
  postcssPlugin: "discard-comments",
  OnceExit(root) {
    root.walkComments((comment) => comment.remove());
  },
};

module.exports = {
  plugins: [
    discardComments,
    require("autoprefixer")({ grid: "autoplace" }),
    require("postcss-sort-media-queries"),
  ],
};
