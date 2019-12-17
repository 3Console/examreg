export default {
  onGetUsdTransactions (state, data) {
    state.usdTransactions = data;
  },

  onGetKycs (state, data) {
    state.kycs = data;
  },

  onGetNotices (state, data) {
    state.notices = data;
  },

  onGetClasses (state, data) {
    state.classes = data;
  },

  onGetShifts (state, data) {
    state.shifts = data;
  },

  onGetLocations (state, data) {
    state.locations = data;
  },
}
