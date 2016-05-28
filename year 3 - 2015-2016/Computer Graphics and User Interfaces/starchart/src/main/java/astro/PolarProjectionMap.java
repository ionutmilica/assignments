package astro;

import java.io.*;
import java.net.URL;
import java.util.ArrayList;

import astro.util.Date;


/**
 * Class for handling Polar Projection  Maps
 * @author Marc Frincu 
 * @since March 15th 2009
 *
 */
public class PolarProjectionMap {

	private static final double RADS = Math.PI / 180.0;

	private NorthPoint northP = null;
	private SouthPoint southP = null;
	private WestPoint westP = null;
	private EastPoint eastP = null;

	private ArrayList<ConstellationLine> conLines = null;
	private ArrayList<ConstellationName> conNames = null;
	private ArrayList<ConstellationStar> conStars = null;
	private ArrayList<ConstellationBoundary> conBoundaries = null;
	private ArrayList<ConstellationBoundaryLine> conBoundaryLines = null;
	private ArrayList<MessierData> messData = null;

	private String fileSep = " ";
	private double lst, lat;

	private Date date = null;

	/**
	 * Constructor
	 * @param longitude the longitude of the place the Polar Projection Map will be built for
	 * @param latitude the latitude of the place the Polar Projection Map will be built for
	 */
	public PolarProjectionMap(double longitude, double latitude) {
		this.conLines = new ArrayList<ConstellationLine>();
		this.conNames = new ArrayList<ConstellationName>();
		this.conStars = new ArrayList<ConstellationStar>();
		this.conBoundaries = new ArrayList<ConstellationBoundary>();
		this.conBoundaryLines = new ArrayList<ConstellationBoundaryLine>();
		this.messData = new ArrayList<MessierData>();

		// date = new Date(year, month, day, hour, longitude);
		date = new Date(longitude);

		this.lst = date.getLST();
		this.lat = latitude;

		this.initCardinalPoints();
	}

	/**
	 * Constructor
	 * @param year the year for which the Polar Projection Map is built for 
	 * @param month the month for which the Polar Projection Map is built for
	 * @param day the day for which the Polar Projection Map is built for
	 * @param hour the hour for which the Polar Projection Map is built for
	 * @param longitude the longitude for which the Polar Projection Map is built for
	 * @param latitude the latitude for which the Polar Projection Map is built for
	 */
	public PolarProjectionMap(int year, int month, int day, double hour,
			double longitude, double latitude) {

		this.conLines = new ArrayList<ConstellationLine>();
		this.conNames = new ArrayList<ConstellationName>();
		this.conStars = new ArrayList<ConstellationStar>();
		this.conBoundaries = new ArrayList<ConstellationBoundary>();
		this.conBoundaryLines = new ArrayList<ConstellationBoundaryLine>();
		this.messData = new ArrayList<MessierData>();

		date = new Date(year, month, day, hour, longitude);

		this.lst = date.getLST();
		this.lat = latitude;

		this.initCardinalPoints();

	}

	/**
	 * Initializes the constellations' lines from a given file. It also computes their coordinates on the map
	 * @param filename the path to the file containing the constellation lines
	 */
	public void initializeConstellationLines(String filename) {
		ConstellationLine conLine = null;
		try {
            URL location = Thread.currentThread().getContextClassLoader().getResource(filename);
            System.out.println(location.getFile());
            InputStreamReader isr = new InputStreamReader(location.openStream());
            LineNumberReader input = new LineNumberReader(isr);
			try {
				String line = null;
				while ((line = input.readLine()) != null) {
					String[] parts = line.split(fileSep);
					conLine = new ConstellationLine();
					conLine.name = parts[0];
					conLine.r1 = Integer.parseInt(parts[1]);
					conLine.d1 = Integer.parseInt(parts[2]);
					conLine.r2 = Integer.parseInt(parts[3]);
					conLine.d2 = Integer.parseInt(parts[4]);
					// System.out.println(conLine.name + " " + conLine.r1 + " "
					// + conLine.d1 + " " + conLine.r2 + " " + conLine.d2);
					conLine = this.initCoordsPP(conLine);
					conLines.add(conLine);
				}
			} finally {
				input.close();
			}
		} catch (IOException ex) {
			ex.printStackTrace();
		}
	}

	/**
	 * Initializes the constellations' boundaries from a given file. It also computes their coordinates on the map
	 * @param filename the path to the file containing the constellation boundaries
	 */
	public void initializeConstellationBoundaries(String filename) {
		ConstellationBoundary conBoundary = null;
		try {
            URL location = Thread.currentThread().getContextClassLoader().getResource(filename);
            System.out.println(location.getFile());
            InputStreamReader isr = new InputStreamReader(location.openStream());
            LineNumberReader input = new LineNumberReader(isr);
			try {
				String line = null;
				while ((line = input.readLine()) != null) {
					String[] parts = line.split(fileSep);
					conBoundary = new ConstellationBoundary();
					conBoundary.ok = (Integer.parseInt(parts[0]) == 0) ? false
							: true;
					conBoundary.r = Integer.parseInt(parts[1]);
					conBoundary.d = Integer.parseInt(parts[2]);
					conBoundaries.add(conBoundary);
				}
				conBoundaryLines = this.initCoordsPP(conBoundaries);
			} finally {
				input.close();
			}
		} catch (IOException ex) {
			ex.printStackTrace();
		}
	}

	/**
	 * Initializes the constellations' names from a given file. It also computer their coordinates on the map
	 * @param filename the path to the file containing the constellation names
	 */
	public void initializeConstellationNames(String filename) {
		ConstellationName conName = null;
		try {
            URL location = Thread.currentThread().getContextClassLoader().getResource(filename);
            System.out.println(location.getFile());
            InputStreamReader isr = new InputStreamReader(location.openStream());
            LineNumberReader input = new LineNumberReader(isr);
			try {
				String line = null;
				while ((line = input.readLine()) != null) {
					String[] parts = line.split(fileSep);
					conName = new ConstellationName();
					conName.name = parts[2];
					conName.r = Integer.parseInt(parts[0]);
					conName.d = Integer.parseInt(parts[1]);
					conName = this.initCoordsPP(conName);
					conNames.add(conName);
				}
			} finally {
				input.close();
			}
		} catch (IOException ex) {
			ex.printStackTrace();
		}
	}

	/**
	 * Initializes the constellations' stars from a given file. It also computes their coordinates on the map
	 * @param filename the path to the file containing the constellation stars
	 */
	public void initializeConstellationStars(String filename) {
		ConstellationStar conStar = null;
		try {
			URL location = Thread.currentThread().getContextClassLoader().getResource(filename);
			System.out.println(location.getFile());
			InputStreamReader isr = new InputStreamReader(location.openStream());
			LineNumberReader input = new LineNumberReader(isr);
			try {
				String line = null;
				while ((line = input.readLine()) != null) {
					String[] parts = line.split(fileSep);
					conStar = new ConstellationStar();
					conStar.name = parts[0];
					conStar.r = Double.parseDouble(parts[1]);
					conStar.d = Double.parseDouble(parts[2]);
                    conStar = this.initCoordsPP(conStar);
					conStars.add(conStar);
				}
			} finally {
				input.close();
			}
		} catch (IOException ex) {
			ex.printStackTrace();
		}
	}

	/**
	 * Initializes the Messier objects
	 * @param filename the path to the file containing the Messier objects
	 */
	public void initializeMessierObjects(String filename) {
		MessierData messObject = null;
		try {
            URL location = Thread.currentThread().getContextClassLoader().getResource(filename);
            System.out.println(location.getFile());
            InputStreamReader isr = new InputStreamReader(location.openStream());
            LineNumberReader input = new LineNumberReader(isr);
			try {
				String line = null;
				while ((line = input.readLine()) != null) {
					String[] parts = line.split(fileSep);
					
					messObject = new MessierData(parts[0], Double
							.parseDouble(parts[1]), Double
							.parseDouble(parts[2]), Double
							.parseDouble(parts[3]));
					messObject = this.initCoordsPP(messObject);
					this.messData.add(messObject);
				}
			} finally {
				input.close();
			}
		} catch (IOException ex) {
			ex.printStackTrace();
		}

	}


	/**
	 * Initializes the cardinal points. It also computer their coordinates on the map
	 */
	private void initCardinalPoints() {
		double zalt = 1.570796;
		double x = Math.cos(0) * Math.tan((zalt - 0) / 2);
		double y = Math.sin(0) * Math.tan((zalt - 0) / 2);

		northP = new NorthPoint(x, y);

		x = Math.cos(90 * PolarProjectionMap.RADS) * Math.tan((zalt - 0) / 2);
		y = Math.sin(90 * PolarProjectionMap.RADS) * Math.tan((zalt - 0) / 2);

		eastP = new EastPoint(x, y);

		x = Math.cos(180 * PolarProjectionMap.RADS) * Math.tan((zalt - 0) / 2);
		y = Math.sin(180 * PolarProjectionMap.RADS) * Math.tan((zalt - 0) / 2);

		southP = new SouthPoint(x, y);

		x = Math.cos(270 * PolarProjectionMap.RADS) * Math.tan((zalt - 0) / 2);
		y = Math.sin(270 * PolarProjectionMap.RADS) * Math.tan((zalt - 0) / 2);

		westP = new WestPoint(x, y);		
	}

	/**
	 * Computes the constellation line coordinates on the map
	 * @param conLine ConstellationLine object containing relevant information
	 */
	private ConstellationLine initCoordsPP(ConstellationLine conLine) {
		boolean plotted = false;
		double h, sina, glat, x, y, a1, a2, aq, bq, cq, dg, xr1, xr2, yr1, yr2, atop, ztop, abot, zbot, M, c;
		double r1, r2, d1, d2;
		double x1 = 0, y1 = 0, z1 = 0, x2 = 0, y2 = 0, z2 = 0;
		boolean p, q;
		double zalt = 1.570796;

		r1 = conLine.r1 / 1000.0 * PolarProjectionMap.RADS * 15;
		d1 = conLine.d1 / 100.0 * PolarProjectionMap.RADS;

		r2 = conLine.r2 / 1000.0 * PolarProjectionMap.RADS * 15;
		d2 = conLine.d2 / 100.0 * PolarProjectionMap.RADS;

		h = this.lst - r1;
		glat = this.lat;

		// System.out.println(conLine.name + " " + r1 + " " + d1 + " " + r2 + "
		// " + d2 + " "+ h + " " + glat);
		sina = Math.sin(d1) * Math.sin(glat) + Math.cos(d1) * Math.cos(glat)
				* Math.cos(h);
		a1 = Math.asin(sina);
		y = -Math.cos(d1) * Math.cos(glat) * Math.sin(h);
		x = Math.sin(d1) - Math.sin(glat) * sina;
		z1 = Math.atan2(y, x);

		h = this.lst - r2;
		glat = this.lat;

		sina = Math.sin(d2) * Math.sin(glat) + Math.cos(d2) * Math.cos(glat)
				* Math.cos(h);
		a2 = Math.asin(sina);
		y = -Math.cos(d2) * Math.cos(glat) * Math.sin(h);
		x = Math.sin(d2) - Math.sin(glat) * sina;
		z2 = Math.atan2(y, x);

		// Case II
		// Both ends are above the horizon
		if ((a1 > 0) && (a2 > 0)) {
			x1 = Math.cos(z1) * Math.tan((zalt - a1) / 2);
			y1 = Math.sin(z1) * Math.tan((zalt - a1) / 2);

			x2 = Math.cos(z2) * Math.tan((zalt - a2) / 2);
			y2 = Math.sin(z2) * Math.tan((zalt - a2) / 2);
			plotted = true;
			conLine.visible = true;
		}
		// Case III
		// An end is above the horizon and another is bellow
		if ((plotted == false) && (a1 * a2) < 0) {
			if (a1 > 0) {
				atop = a1;
				ztop = z1;
				abot = a2;
				zbot = z2;
			} else {
				atop = a2;
				ztop = z2;
				abot = a1;
				zbot = z1;
			}
			x1 = Math.cos(ztop) * Math.tan((zalt - atop) / 2);
			y1 = Math.sin(ztop) * Math.tan((zalt - atop) / 2);

			x2 = Math.cos(zbot) * Math.tan((zalt - abot) / 2);
			y2 = Math.sin(zbot) * Math.tan((zalt - abot) / 2);

			M = (y1 - y2) / (x1 - x2);
			c = y1 - M * x1;

			aq = (M * M + 1);
			bq = 2 * c * M;
			cq = c * c - 1;

			// Case V

			dg = bq * bq - 4 * aq * cq;

			xr1 = (-bq + Math.sqrt(dg)) / (2 * aq);
			xr2 = (-bq - Math.sqrt(dg)) / (2 * aq);
			yr1 = M * xr1 + c;
			yr2 = M * xr2 + c;

			p = between(x1, x2, xr1);
			q = between(y1, y2, yr1);

			if ((p == true) && (q == true)) {
				x2 = xr1;
				y2 = yr1;
			}
			p = between(x1, x2, xr2);
			q = between(y1, y2, yr2);

			if ((p == true) && (q == true)) {
				x2 = xr2;
				y2 = yr2;
			}
			conLine.visible = true;
		}

		if (conLine.visible) {
			conLine.x1 = x1;
			conLine.y1 = y1;
			conLine.z1 = 0.0;

			conLine.x2 = x2;
			conLine.y2 = y2;
			conLine.z2 = 0.0;
		}
		return conLine;
	}

	/**
	 * Computes the constellation boundaries coordinates on the map
	 * @param conBoundaries ConstellationBoundary object containing relevant information
	 */
	private ArrayList<ConstellationBoundaryLine> initCoordsPP(
			ArrayList<ConstellationBoundary> conBoundaries) {
		boolean plotted = false;
		double h, sina, glat, x, y, a1, a2, aq, bq, cq, dg, xr1, xr2, yr1, yr2, atop, ztop, abot, zbot, M, c;
		double r1, r2, d1, d2;
		double x1 = 0, y1 = 0, z1 = 0, x2 = 0, y2 = 0, z2 = 0;
		boolean p, q;
		double zalt = 1.570796;

		ArrayList<ConstellationBoundaryLine> conBoundaryLines = new ArrayList<ConstellationBoundaryLine>();
		ConstellationBoundaryLine cBoundaryLine = null;

		for (int i = 0; i < conBoundaries.size() - 1; i++) {
			plotted = false;
			if (conBoundaries.get(i + 1).isOk()) {

				cBoundaryLine = new ConstellationBoundaryLine();
				
				r1 = conBoundaries.get(i).r / 1000.0 * PolarProjectionMap.RADS
						* 15;
				d1 = conBoundaries.get(i).d / 100.0 * PolarProjectionMap.RADS;

				r2 = conBoundaries.get(i + 1).r / 1000.0
						* PolarProjectionMap.RADS * 15;
				d2 = conBoundaries.get(i + 1).d / 100.0 * PolarProjectionMap.RADS;

				h = this.lst - r1;
				glat = this.lat;

				// System.out.println(r1 + " " + d1 + " " +
				// r2 + " " + d2 + " "+ h + " " + glat);
				sina = Math.sin(d1) * Math.sin(glat) + Math.cos(d1)
						* Math.cos(glat) * Math.cos(h);
				a1 = Math.asin(sina);
				y = -Math.cos(d1) * Math.cos(glat) * Math.sin(h);
				x = Math.sin(d1) - Math.sin(glat) * sina;
				z1 = Math.atan2(y, x);

				h = this.lst - r2;
				glat = this.lat;

				sina = Math.sin(d2) * Math.sin(glat) + Math.cos(d2)
						* Math.cos(glat) * Math.cos(h);
				a2 = Math.asin(sina);
				y = -Math.cos(d2) * Math.cos(glat) * Math.sin(h);
				x = Math.sin(d2) - Math.sin(glat) * sina;
				z2 = Math.atan2(y, x);

				// Case II
				// Both ends are above the horizon
				if ((a1 > 0) && (a2 > 0)) {
					x1 = Math.cos(z1) * Math.tan((zalt - a1) / 2);
					y1 = Math.sin(z1) * Math.tan((zalt - a1) / 2);

					x2 = Math.cos(z2) * Math.tan((zalt - a2) / 2);
					y2 = Math.sin(z2) * Math.tan((zalt - a2) / 2);
					plotted = true;
					cBoundaryLine.visible = true;
				}
				// Case III
				// An end is above the horizon and another is bellow
				if ((plotted == false) && (a1 * a2) < 0) {
					if (a1 > 0) {
						atop = a1;
						ztop = z1;
						abot = a2;
						zbot = z2;
					} else {
						atop = a2;
						ztop = z2;
						abot = a1;
						zbot = z1;
					}
					x1 = Math.cos(ztop) * Math.tan((zalt - atop) / 2);
					y1 = Math.sin(ztop) * Math.tan((zalt - atop) / 2);

					x2 = Math.cos(zbot) * Math.tan((zalt - abot) / 2);
					y2 = Math.sin(zbot) * Math.tan((zalt - abot) / 2);

					M = (y1 - y2) / (x1 - x2);
					c = y1 - M * x1;

					aq = (M * M + 1);
					bq = 2 * c * M;
					cq = c * c - 1;

					// Case V

					dg = bq * bq - 4 * aq * cq;

					xr1 = (-bq + Math.sqrt(dg)) / (2 * aq);
					xr2 = (-bq - Math.sqrt(dg)) / (2 * aq);
					yr1 = M * xr1 + c;
					yr2 = M * xr2 + c;

					p = between(x1, x2, xr1);
					q = between(y1, y2, yr1);

					if ((p == true) && (q == true)) {
						x2 = xr1;
						y2 = yr1;
					}
					p = between(x1, x2, xr2);
					q = between(y1, y2, yr2);

					if ((p == true) && (q == true)) {
						x2 = xr2;
						y2 = yr2;
					}
					cBoundaryLine.visible = true;
				}

				cBoundaryLine.x1 = x1;
				cBoundaryLine.y1 = y1;
				cBoundaryLine.z1 = 0;
				cBoundaryLine.x2 = x2;
				cBoundaryLine.y2 = y2;
				cBoundaryLine.z2 = 0;
				conBoundaryLines.add(cBoundaryLine);
			}
		}
		return conBoundaryLines;
	}

	/**
	 * Computes the Messier coordinates on the map
	 * 
	 * @param conStar
	 *            ConstellationStar object containing relevant information
	 */
	private MessierData initCoordsPP(MessierData messObject) {
		double h, sina, glat, x, y, z, a;
		double r1, d1;
		double nudge = 2 / 100.0;
		double zalt = 1.570796;

		r1 = messObject.getRA() * PolarProjectionMap.RADS * 15.04107;
		d1 = messObject.getDec() * PolarProjectionMap.RADS;

		h = this.lst - r1;
		glat = this.lat;
		sina = Math.sin(d1) * Math.sin(glat) + Math.cos(d1) * Math.cos(glat)
				* Math.cos(h);
		a = Math.asin(sina);
		y = -Math.cos(d1) * Math.cos(glat) * Math.sin(h);
		x = Math.sin(d1) - Math.sin(glat) * sina;
		z = Math.atan2(y, x);

		if (a > 0) {
			x = Math.cos(z) * Math.tan((zalt - a) / 2);
			y = Math.sin(z) * Math.tan((zalt - a) / 2);

			if (x > 0)
				x = x + 1 * nudge;
			else
				x = x + -1 * nudge;
			if (y > 0)
				y = y + 1 * nudge;
			else
				y = y + -1 * nudge;

			messObject.setVisible(true);
		}

		messObject.setX(x);
		messObject.setY(y);
		return messObject;
	}

	/**
	 * Computes the constellation name coordinates on the map
	 * @param conName ConstellationName object containing relevant information
	 */
	private ConstellationName initCoordsPP(ConstellationName conName) {
		double h, sina, glat, x, y, z, a;
		double r1, d1;
		double zalt = 1.570796;

		r1 = conName.r / 1000.0 * PolarProjectionMap.RADS * 15;
		d1 = conName.d / 100.0 * PolarProjectionMap.RADS;

		h = this.lst - r1;
		glat = this.lat;
		sina = Math.sin(d1) * Math.sin(glat) + Math.cos(d1) * Math.cos(glat)
				* Math.cos(h);
		a = Math.asin(sina);
		y = -Math.cos(d1) * Math.cos(glat) * Math.sin(h);
		x = Math.sin(d1) - Math.sin(glat) * sina;
		z = Math.atan2(y, x);

		if (a > 0) {
			x = Math.cos(z) * Math.tan((zalt - a) / 2);
			y = Math.sin(z) * Math.tan((zalt - a) / 2);
			conName.visible = true;
		}

		conName.x = x;
		conName.y = y;
		conName.z = 0;
		return conName;
	}


	/**
	 * Computes the constellation star coordinates on the map
	 * @param conStar ConstellationStar object containing relevant information
	 */
	private ConstellationStar initCoordsPP(ConstellationStar conStar) {
		double h, sina, glat, x, y, z, a;
		double r1, d1;
		double nudge = 2 / 100.0;
		double zalt = 1.570796;

		r1 = conStar.r * PolarProjectionMap.RADS * 15;
		d1 = conStar.d * PolarProjectionMap.RADS;

		h = this.lst - r1;
		glat = this.lat;
		sina = Math.sin(d1) * Math.sin(glat) + Math.cos(d1) * Math.cos(glat)
				* Math.cos(h);
		a = Math.asin(sina);
		y = -Math.cos(d1) * Math.cos(glat) * Math.sin(h);
		x = Math.sin(d1) - Math.sin(glat) * sina;
		z = Math.atan2(y, x);

		if (a > 0) {
			x = Math.cos(z) * Math.tan((zalt - a) / 2);
			y = Math.sin(z) * Math.tan((zalt - a) / 2);

			if (x > 0)
				x = x + 1 * nudge;
			else
				x = x + -1 * nudge;
			if (y > 0)
				y = y + 1 * nudge;
			else
				y = y + -1 * nudge;

			conStar.visible = true;
		}

		conStar.x = x;
		conStar.y = y;
		conStar.z = 0;
		return conStar;
	}


	/**
	 * Verifies whether a number is between another two numbers
	 * @param a the first number
	 * @param b the second number
	 * @param x the number we want to check
	 */
	private boolean between(double a, double b, double x) {
		double a1 = a, b1 = b, c;

		if (a1 > b1) {
			c = b1;
			b1 = a1;
			a1 = c;
		}

		if ((a1 < x) && (x < b1))
			return true;
		return false;
	}

	
	/**
	 * Class holding information about a single constellation line
	 * @author Marc Frincu
	 * @since March 15th 2009
	 *
	 */
	public class ConstellationLine {
		int r1, d1, r2, d2;
		String name;
		double x1, y1, z1, x2, y2, z2;
		boolean visible;

		public ConstellationLine() {
			this.visible = false;
		}

		public int getRA1() {
			return r1;
		}

		public int getDec1() {
			return d1;
		}

		public String getName() {
			return name;
		}

		public double getPosX1() {
			return x1;
		}

		public double getPosY1() {
			return y1;
		}

		public double getPosZ1() {
			return z1;
		}

		public int getRA2() {
			return r2;
		}

		public int getDec2() {
			return d2;
		}

		public double getPosX2() {
			return x2;
		}

		public double getPosY2() {
			return y2;
		}

		public double getPosZ2() {
			return z2;
		}

		public boolean isVisible() {
			return visible;
		}
	}


	/**
	 * Class holding information about a single constellation boundary point
	 * @author Marc Frincu
	 * @since March 15th 2009
	 *
	 */
	private class ConstellationBoundary {
		int r, d;
		boolean ok, visible;

		ConstellationBoundary() {
			this.ok = false;
		}

		public double getRA() {
			return r;
		}

		public double getDec() {
			return d;
		}

		public boolean isOk() {
			return ok;
		}

		public boolean isVisible() {
			return visible;
		}
	}

	/**
	 * Class holding information about a single constellation boundary line
	 * @author Marc Frincu
	 * @since March 15th 2009
	 *
	 */
	public class ConstellationBoundaryLine {
		double x1, y1, z1, x2, y2, z2;
		boolean visible;

		ConstellationBoundaryLine() {
			this.visible = false;
		}

		public double getPosX1() {
			return x1;
		}
		
		public double getPosY1() {
			return y1;
		}

		public double getPosZ1() {
			return z1;
		}

		public double getPosX2() {
			return x2;
		}

		public double getPosY2() {
			return y2;
		}

		public double getPosZ2() {
			return z2;
		}

		public boolean isVisible() {
			return visible;
		}

	}

	/**
	 * Class holding information about a single constellation name
	 * @author Marc Frincu
	 * @since March 15th 2009
	 *
	 */
	public class ConstellationName {
		int r, d;
		String name;
		double x, y, z;
		boolean visible;

		public ConstellationName() {
			this.visible = false;
		}

		public float getRA() {
			return r;
		}

		public float getDec() {
			return d;
		}

		public String getName() {
			return name;
		}

		public double getPosX() {
			return x;
		}

		public double getPosY() {
			return y;
		}

		public double getPosZ() {
			return z;
		}

		public boolean isVisible() {
			return visible;
		}

	}

	
	/**
	 * Class holding information about a single constellation star
	 * @author Marc Frincu
	 * @since March 15th 2009
	 *
	 */
	public class ConstellationStar {
		double r, d;
		String name;
		double x, y, z;
		boolean visible;

		public ConstellationStar() {
			this.visible = false;
		}

		public boolean isVisible() {
			return visible;
		}

		public double getRA() {
			return r;
		}

		public double getDec() {
			return d;
		}

		public String getName() {
			return name;
		}

		public double getPosX() {
			return x;
		}

		public double getPosY() {
			return y;
		}

		public double getPosZ() {
			return z;
		}
	}

	/**
	 * Class holding information about a single Messier object.
	 * @author Marc Frincu
	 * @since 2009
	 */
	public class MessierData {
		private String name;
		private double RA, Dec, magnitude, x, y;
		private boolean visible = false;
	
		public MessierData(String name, double RA, double Dec, double magnitude){
			this.name = name;
			this.RA = RA;
			this.Dec = Dec;
			this.magnitude = magnitude;
		}

		public String getName() {
			return name;
		}

		public double getRA() {
			return RA;
		}

		public double getDec() {
			return Dec;
		}

		public double getMagnitude() {
			return magnitude;
		}

		public double getX() {
			return x;
		}

		public void setX(double x) {
			this.x = x;
		}

		public double getY() {
			return y;
		}

		public void setY(double y) {
			this.y = y;
		}

		public boolean isVisible() {
			return visible;
		}

		public void setDec(double dec) {
			Dec = dec;
		}

		public void setVisible(boolean visible) {
			this.visible = visible;
		}
	}



	/**
	 * Class holding information about the North cardinal point
	 * @author Marc Frincu
	 * @since March 15th 2009
	 *
	 */
	public class NorthPoint {
		double x, y;

		NorthPoint(double x, double y) {
			this.x = x;
			this.y = y;
		}

		public double getPosX() {
			return x;
		}

		public double getPosY() {
			return y;
		}
	}


	/**
	 * Class holding information about the South cardinal point
	 * @author Marc Frincu
	 * @since March 15th 2009
	 *
	 */
	public class SouthPoint {
		double x, y;

		SouthPoint(double x, double y) {
			this.x = x;
			this.y = y;
		}

		public double getPosX() {
			return x;
		}

		public double getPosY() {
			return y;
		}
	}


	/**
	 * Class holding information about the East cardinal point
	 * @author Marc Frincu
	 * @since March 15th 2009
	 *
	 */
	public class EastPoint {
		double x, y;

		EastPoint(double x, double y) {
			this.x = x;
			this.y = y;
		}

		public double getPosX() {
			return x;
		}

		public double getPosY() {
			return y;
		}
	}


	/**
	 * Class holding information about the West cardinal point
	 * @author Marc Frincu
	 * @since March 15th 2009
	 *
	 */
	public class WestPoint {
		double x, y;

		WestPoint(double x, double y) {
			this.x = x;
			this.y = y;
		}

		public double getPosX() {
			return x;
		}

		public double getPosY() {
			return y;
		}
	}

	
	
	/**
	 * Gets the separator for line fields 
	 */ 
	public String getFileSep() {
		return fileSep;
	}


	/**
	 * Sets the separator for line fields
	 * @param fileSep the new line field separator 
	 */ 
	public void setFileSep(String fileSep) {
		this.fileSep = fileSep;
	}
	
	/**
	 * Returns a list of constellation name objects.
	 * @return the list of ConstellationName objects
	 */

	public ArrayList<ConstellationName> getConNames() {
		return conNames;
	}



	/**
	 * Returns a list of constellation line objects.
	 * @return the list of ConstellationLine objects
	 */
	public ArrayList<ConstellationLine> getConLines() {
		return conLines;
	}
	
	/**
	 * Returns a list of constellation star objects.
	 * @return the list of ConstellationStar objects
	 */

	public ArrayList<ConstellationStar> getConStars() {
		return conStars;
	}
	
	/**
	 * Returns a list of constellation boundary line objects.
	 * @return the list of ConstellationBoundaryLine objects
	 */

	public ArrayList<ConstellationBoundaryLine> getConBoundaryLines() {
		return conBoundaryLines;
	}

	/**
	* Returns the list of Messier objects
	* @return the list of MessierData object
	*/
	public ArrayList<MessierData> getMessierObjects() {
		return messData;
	}
	
	/**
	 * Returns the object holding information about the North cardinal point
	 * @return a NorthPoint object
	 */
	public NorthPoint getNorthP() {
		return northP;
	}
	
	/**
	 * Returns the object holding information about the South cardinal point
	 * @return a SouthPoint object
	 */

	public SouthPoint getSouthP() {
		return southP;
	}
	
	/**
	 * Returns the object holding information about the West cardinal point
	 * @return a WestPoint object
	 */

	public WestPoint getWestP() {
		return westP;
	}
	
	/**
	 * Returns the object holding information about the East cardinal point
	 * @return a EastPoint object
	 */

	public EastPoint getEastP() {
		return eastP;
	}

}
