package astro.util;

import java.text.SimpleDateFormat;
import java.util.Calendar;

/**
 * Handles some basic dates such as LST, Day Number, Current Date/Time
 * @author Marc Frincu
 * @since March 15th 2009
 *
 */
public class Date {

	private static final String DATE_FORMAT_NOW = "yyyy-MM-dd";
	private static final String TIME_FORMAT_NOW = "HH:mm:ss";

	private double d, lst;

	public Date(int year, int month, int day, double hour, double longitude) {

		this.computeDayNumberAndLST(year, month, day, hour, longitude);
		
	}

	public Date(double longitude) {
		final String[] date = this.getCurrentDate().split("-");
		final String[] time = this.getCurrentTime().split(":");
				
		this.computeDayNumberAndLST(Integer.parseInt(date[0]), Integer.parseInt(date[1]), Integer
				.parseInt(date[2]), Double.parseDouble(time[0])
				, longitude);
	}
	
	private void computeDayNumberAndLST(int year, int month, int day, double hour, double longitude) {
		this.d = 367 * (year) - (7 * ((year) + (((month) + 9) / 12))) / 4
		+ (275 * (month)) / 9 + day - 730530;
		this.d = this.d + (int)hour / 24;
		this.lst = this.rev(100.46 + 0.985647 * this.d + longitude + hour * 15.04107);
		this.lst = this.lst / 15.04107;		
	}

	private double rev(double x) {
		double rv;
		rv = x - ((int)x / 360) * 360;
		if (rv < 0) {
			rv = rv + 360;
		}
		return rv;
	}

	public double getDayNumber() {
		return this.d;
	}

	public double getLST() {
		return this.lst;
	}

	public String getCurrentDate() {
		Calendar cal = Calendar.getInstance();
		SimpleDateFormat sdf = new SimpleDateFormat(DATE_FORMAT_NOW);
		return sdf.format(cal.getTime());
	}

	public String getCurrentTime() {
		Calendar cal = Calendar.getInstance();
		SimpleDateFormat sdf = new SimpleDateFormat(TIME_FORMAT_NOW);
		return sdf.format(cal.getTime());
	}
}
